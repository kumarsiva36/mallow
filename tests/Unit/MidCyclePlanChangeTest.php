<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionSegment;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidCyclePlanChangeTest extends TestCase
{
    use RefreshDatabase;

    protected BillingService $billingService;
    protected SubscriptionService $subscriptionService;
    protected UsageService $usageService;

    protected Merchant $merchant;
    protected Customer $customer;
    protected Plan $planA;
    protected Plan $planB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usageService = app(UsageService::class);
        $this->subscriptionService = app(SubscriptionService::class);
        $this->billingService = app(BillingService::class);

        $this->merchant = Merchant::create([
            'name' => 'Unit Cloud',
            'slug' => 'unit-cloud',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        // Plan A: $30/mo, 1000 units included, $0.05 overage, 30 days
        $this->planA = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Plan A',
            'code' => 'plan-a',
            'base_price' => 30.00,
            'cycle_days' => 30,
            'included_usage_units' => 1000,
            'overage_rate_per_unit' => 0.0500,
            'prorate_allowance' => true,
        ]);

        // Plan B: $90/mo, 10000 units included, $0.02 overage, 30 days
        $this->planB = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Plan B',
            'code' => 'plan-b',
            'base_price' => 90.00,
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0200,
            'prorate_allowance' => true,
        ]);
    }

    public function test_mid_cycle_upgrade_calculates_segmented_proration_and_overage(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-10-01 00:00:00');
        $switchDate = Carbon::parse('2026-09-11 00:00:00'); // 10 days on Plan A, 20 days on Plan B

        // Start subscription on Plan A
        $sub = $this->subscriptionService->subscribe($this->customer, $this->planA, $cycleStart, $cycleStart);
        $sub->update(['current_cycle_end' => $cycleEnd]);
        $sub->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Upgrade on day 10 to Plan B
        $this->subscriptionService->changePlan($sub, $this->planB, $switchDate);
        $sub->segments()->where('plan_id', $this->planB->id)->update([
            'ends_at' => $cycleEnd,
            'billing_cycle_end' => $cycleEnd,
        ]);

        // Record usage for Segment 1 (Plan A): 600 units (allowance is 1000 * 10/30 = 333 units -> 267 overage)
        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            600,
            'api_calls',
            'seg1_usage',
            Carbon::parse('2026-09-05 12:00:00')
        );

        // Record usage for Segment 2 (Plan B): 8000 units (allowance is 10000 * 20/30 = 6667 units -> 1333 overage)
        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            8000,
            'api_calls',
            'seg2_usage',
            Carbon::parse('2026-09-20 12:00:00')
        );

        $details = $this->billingService->calculateBillingDetails($sub);

        $this->assertTrue($details['is_segmented']);
        $this->assertCount(2, $details['segments']);

        // Check Segment 1 (Plan A)
        $seg1 = $details['segments'][0];
        $this->assertEquals('Plan A', $seg1['plan_name']);
        $this->assertEquals(10, $seg1['active_days']);
        $this->assertEquals(0.3333, $seg1['proration_ratio']);
        $this->assertEquals(10.00, $seg1['base_amount']); // $30 * 0.3333 = $10.00
        $this->assertEquals(333, $seg1['effective_allowance']); // 1000 * 0.3333 = 333
        $this->assertEquals(600, $seg1['actual_units']);
        $this->assertEquals(267, $seg1['overage_units']); // 600 - 333 = 267
        $this->assertEquals(13.35, $seg1['overage_amount']); // 267 * 0.05 = 13.35

        // Check Segment 2 (Plan B)
        $seg2 = $details['segments'][1];
        $this->assertEquals('Plan B', $seg2['plan_name']);
        $this->assertEquals(20, $seg2['active_days']);
        $this->assertEquals(0.6667, $seg2['proration_ratio']);
        $this->assertEquals(60.00, $seg2['base_amount']); // $90 * 0.6667 = $60.00
        $this->assertEquals(6667, $seg2['effective_allowance']); // 10000 * 0.6667 = 6667
        $this->assertEquals(8000, $seg2['actual_units']);
        $this->assertEquals(1333, $seg2['overage_units']); // 8000 - 6667 = 1333
        $this->assertEquals(26.66, $seg2['overage_amount']); // 1333 * 0.02 = 26.66

        // Check Totals
        $this->assertEquals(70.00, $details['base_amount']); // $10.00 + $60.00 = $70.00
        $this->assertEquals(40.01, $details['overage_amount']); // $13.35 + $26.66 = $40.01
        $this->assertEquals(110.01, $details['total_amount']);
    }

    public function test_mid_cycle_downgrade_with_zero_overage(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-10-01 00:00:00');
        $switchDate = Carbon::parse('2026-09-16 00:00:00'); // 15 days on Plan B, 15 days on Plan A

        $sub = $this->subscriptionService->subscribe($this->customer, $this->planB, $cycleStart, $cycleStart);
        $sub->update(['current_cycle_end' => $cycleEnd]);
        $sub->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Downgrade to Plan A halfway through cycle
        $this->subscriptionService->changePlan($sub, $this->planA, $switchDate);
        $sub->segments()->where('plan_id', $this->planA->id)->update([
            'ends_at' => $cycleEnd,
            'billing_cycle_end' => $cycleEnd,
        ]);

        // Record minimal usage: 200 units in segment 1, 100 units in segment 2 (well within allowance)
        $this->usageService->recordEvent($this->merchant->id, $this->customer->id, 200, 'api_calls', 'seg1', Carbon::parse('2026-09-08 10:00:00'));
        $this->usageService->recordEvent($this->merchant->id, $this->customer->id, 100, 'api_calls', 'seg2', Carbon::parse('2026-09-22 10:00:00'));

        $details = $this->billingService->calculateBillingDetails($sub);

        $this->assertTrue($details['is_segmented']);
        $this->assertEquals(0.00, $details['overage_amount']);
        // Plan B 50% ($45) + Plan A 50% ($15) = $60.00
        $this->assertEquals(60.00, $details['base_amount']);
        $this->assertEquals(60.00, $details['total_amount']);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingProrationTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Customer $customer;
    protected Plan $plan;
    protected UsageService $usageService;
    protected SubscriptionService $subscriptionService;
    protected BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->merchant = Merchant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Acme Test',
            'email' => 'acme@test.com',
        ]);

        $this->plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Pro Tier',
            'code' => 'pro-tier',
            'base_price' => 100.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0500,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $this->usageService = app(UsageService::class);
        $this->subscriptionService = app(SubscriptionService::class);
        $this->billingService = app(BillingService::class);
    }

    public function test_proration_ratio_calculation_for_full_and_mid_cycle(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-09-30 23:59:59');

        // Case 1: Started on Day 1 (Full Cycle)
        $ratioFull = $this->billingService->calculateProrationRatio(
            $cycleStart,
            $cycleStart,
            $cycleEnd,
            30
        );
        $this->assertEquals(1.0, $ratioFull);

        // Case 2: Started on Day 16 (15 days remaining in 30-day cycle -> 50%)
        $midCycleStart = Carbon::parse('2026-09-16 00:00:00');
        $ratioMid = $this->billingService->calculateProrationRatio(
            $midCycleStart,
            $cycleStart,
            $cycleEnd,
            30
        );
        $this->assertEquals(0.5000, $ratioMid);
    }

    public function test_full_cycle_billing_with_no_overage(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-09-30 23:59:59');

        $subscription = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $cycleStart,
            'current_cycle_start' => $cycleStart,
            'current_cycle_end' => $cycleEnd,
        ]);

        // Record usage within included quota (3,000 out of 10,000 units)
        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            3000,
            'api_calls',
            'test_evt_1',
            $cycleStart->copy()->addDays(5)
        );

        $details = $this->billingService->calculateBillingDetails($subscription);

        $this->assertFalse($details['is_prorated']);
        $this->assertEquals(1.0, $details['proration_ratio']);
        $this->assertEquals(100.00, $details['base_amount']);
        $this->assertEquals(10000, $details['effective_allowance']);
        $this->assertEquals(3000, $details['actual_units']);
        $this->assertEquals(0, $details['overage_units']);
        $this->assertEquals(0.00, $details['overage_amount']);
        $this->assertEquals(100.00, $details['total_amount']);
    }

    public function test_mid_cycle_start_prorates_base_price_and_included_units(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-09-30 23:59:59');
        $midStart = Carbon::parse('2026-09-16 00:00:00'); // 15 active days out of 30 = 0.5000 ratio

        $subscription = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $midStart,
            'current_cycle_start' => $cycleStart,
            'current_cycle_end' => $cycleEnd,
        ]);

        // Record 8,000 units during the active period
        // Prorated allowance should be 50% of 10,000 = 5,000 units
        // Overage should be 8,000 - 5,000 = 3,000 units
        // Overage fee = 3,000 * $0.05 = $150.00
        // Base fee = 50% of $100 = $50.00
        // Total = $50.00 + $150.00 = $200.00
        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            8000,
            'api_calls',
            'test_evt_mid_1',
            $midStart->copy()->addDays(2)
        );

        $details = $this->billingService->calculateBillingDetails($subscription);

        $this->assertTrue($details['is_prorated']);
        $this->assertEquals(0.5000, $details['proration_ratio']);
        $this->assertEquals(50.00, $details['base_amount']);
        $this->assertEquals(5000, $details['effective_allowance']);
        $this->assertEquals(8000, $details['actual_units']);
        $this->assertEquals(3000, $details['overage_units']);
        $this->assertEquals(150.00, $details['overage_amount']);
        $this->assertEquals(200.00, $details['total_amount']);
    }

    public function test_unprorated_allowance_option(): void
    {
        $this->plan->update(['prorate_allowance' => false]);

        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-09-30 23:59:59');
        $midStart = Carbon::parse('2026-09-16 00:00:00');

        $subscription = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $midStart,
            'current_cycle_start' => $cycleStart,
            'current_cycle_end' => $cycleEnd,
        ]);

        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            8000,
            'api_calls',
            'test_unprorated',
            $midStart->copy()->addDay()
        );

        $details = $this->billingService->calculateBillingDetails($subscription);

        // Base price is prorated to 50.00, but allowance stays full 10,000 units
        $this->assertEquals(50.00, $details['base_amount']);
        $this->assertEquals(10000, $details['effective_allowance']);
        $this->assertEquals(0, $details['overage_units']);
        $this->assertEquals(50.00, $details['total_amount']);
    }
}

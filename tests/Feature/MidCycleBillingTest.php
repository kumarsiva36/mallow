<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidCycleBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_end_to_end_mid_cycle_plan_change_and_invoice_generation(): void
    {
        $merchant = Merchant::create([
            'name' => 'API Cloud',
            'slug' => 'api-cloud',
            'currency' => 'USD',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'name' => 'Feature Test Corp',
            'email' => 'feature@testcorp.com',
        ]);

        $starter = Plan::create([
            'merchant_id' => $merchant->id,
            'name' => 'Starter Tier',
            'code' => 'starter',
            'base_price' => 30.00,
            'cycle_days' => 30,
            'included_usage_units' => 1000,
            'overage_rate_per_unit' => 0.0500,
            'prorate_allowance' => true,
        ]);

        $growth = Plan::create([
            'merchant_id' => $merchant->id,
            'name' => 'Growth Tier',
            'code' => 'growth',
            'base_price' => 90.00,
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0200,
            'prorate_allowance' => true,
        ]);

        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-10-01 00:00:00');

        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);
        $sub = $subscriptionService->subscribe($customer, $starter, $cycleStart, $cycleStart);
        $sub->update(['current_cycle_end' => $cycleEnd]);
        $sub->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // 1. Record usage on Starter Tier (500 units)
        $usageService = app(UsageService::class);
        $usageService->recordEvent($merchant->id, $customer->id, 500, 'api_calls', 'e2e_1', Carbon::parse('2026-09-05 10:00:00'));

        // 2. Change Plan via API endpoint: POST /api/v1/merchants/{merchant}/subscriptions/{subscription}/change-plan
        $switchDate = Carbon::parse('2026-09-11 00:00:00');
        $response = $this->postJson("/api/v1/merchants/{$merchant->id}/subscriptions/{$sub->id}/change-plan", [
            'plan_id' => $growth->id,
            'effective_at' => $switchDate->toDateTimeString(),
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.plan_id', $growth->id);
        $this->assertCount(2, $sub->fresh()->segments);

        // Adjust segment ends_at to cycleEnd for test boundary
        $sub->segments()->where('plan_id', $growth->id)->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // 3. Record usage on Growth Tier (7,000 units)
        $usageService->recordEvent($merchant->id, $customer->id, 7000, 'api_calls', 'e2e_2', Carbon::parse('2026-09-20 10:00:00'));

        // 4. Generate cycle-end invoice via API endpoint
        $invResponse = $this->postJson("/api/v1/merchants/{$merchant->id}/subscriptions/{$sub->id}/generate-invoice");

        $invResponse->assertStatus(201);
        $invoiceData = $invResponse->json('data');

        // Invoice should contain line items for both Starter and Growth tiers
        $items = $invoiceData['items'];
        $this->assertNotEmpty($items);

        $descriptions = array_column($items, 'description');
        $this->assertTrue(collect($descriptions)->contains(fn($d) => str_contains($d, 'Starter Tier Base Subscription')));
        $this->assertTrue(collect($descriptions)->contains(fn($d) => str_contains($d, 'Growth Tier Base Subscription')));
        $this->assertTrue(collect($descriptions)->contains(fn($d) => str_contains($d, '[Starter Tier] Included allowance')));
        $this->assertTrue(collect($descriptions)->contains(fn($d) => str_contains($d, '[Growth Tier] Included allowance')));
    }
}

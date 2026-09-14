<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingCycleTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Customer $customer;
    protected Plan $plan;
    protected UsageService $usageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->merchant = Merchant::create([
            'name' => 'SaaS Host Inc',
            'slug' => 'saas-host',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Mega Corp',
            'email' => 'billing@megacorp.com',
        ]);

        $this->plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Enterprise Growth',
            'code' => 'enterprise-growth',
            'base_price' => 200.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 20000,
            'overage_rate_per_unit' => 0.0250,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $this->usageService = app(UsageService::class);
    }

    public function test_generate_invoice_for_mid_cycle_subscription_with_overage(): void
    {
        $cycleStart = Carbon::parse('2026-09-01 00:00:00');
        $cycleEnd = Carbon::parse('2026-09-30 23:59:59');
        $startsAt = Carbon::parse('2026-09-16 00:00:00'); // 15 days active = 50% proration

        $subscription = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'current_cycle_start' => $cycleStart,
            'current_cycle_end' => $cycleEnd,
        ]);

        // Record 25,000 units during active period
        // Effective allowance: 50% of 20,000 = 10,000 units
        // Overage: 25,000 - 10,000 = 15,000 units
        // Overage fee: 15,000 * $0.0250 = $375.00
        // Base fee: 50% of $200.00 = $100.00
        // Total invoice amount: $100.00 + $375.00 = $475.00
        $this->usageService->recordEvent(
            $this->merchant->id,
            $this->customer->id,
            25000,
            'api_calls',
            'test_invoice_evt_1',
            $startsAt->copy()->addDays(3)
        );

        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/subscriptions/{$subscription->id}/generate-invoice", [
            'advance_cycle' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Invoice generated successfully',
                'data' => [
                    'merchant_id' => $this->merchant->id,
                    'customer_id' => $this->customer->id,
                    'base_amount' => '100.00',
                    'overage_amount' => '375.00',
                    'total_amount' => '475.00',
                    'proration_ratio' => '0.5000',
                    'currency' => 'USD',
                ]
            ]);

        // Verify invoice line items in database
        $invoice = Invoice::where('subscription_id', $subscription->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(3, $invoice->items()->count());

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'type' => 'base_fee',
            'amount' => 100.00,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'type' => 'allowance',
            'quantity' => 10000,
            'amount' => 0.00,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'type' => 'overage',
            'quantity' => 15000,
            'amount' => 375.00,
        ]);

        // Verify subscription cycle was advanced
        $subscription->refresh();
        $this->assertTrue($subscription->current_cycle_start->gt($cycleEnd));
    }

    public function test_automated_process_cycle_bills_only_due_subscriptions(): void
    {
        $now = Carbon::now();
        $dueEnd = $now->copy()->subMinutes(10); // Expired 10 mins ago -> due!
        $futureEnd = $now->copy()->addDays(15); // Not due yet

        // Subscription 1: Due
        $subDue = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $dueEnd->copy()->subDays(30),
            'current_cycle_start' => $dueEnd->copy()->subDays(30),
            'current_cycle_end' => $dueEnd,
        ]);

        // Subscription 2: Not Due
        $cust2 = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Future Corp',
            'email' => 'future@test.com',
        ]);
        $subFuture = Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $cust2->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => $now->copy()->subDays(15),
            'current_cycle_start' => $now->copy()->subDays(15),
            'current_cycle_end' => $futureEnd,
        ]);

        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/billing/process-cycle", [
            'as_of' => $now->toDateTimeString(),
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'count' => 1,
            ]);

        $this->assertDatabaseHas('invoices', [
            'subscription_id' => $subDue->id,
        ]);

        $this->assertDatabaseMissing('invoices', [
            'subscription_id' => $subFuture->id,
        ]);
    }

    public function test_tenant_isolation_guards(): void
    {
        $otherMerchant = Merchant::create([
            'name' => 'Rival Merchant',
            'slug' => 'rival-merchant',
            'currency' => 'EUR',
        ]);

        $otherCustomer = Customer::create([
            'merchant_id' => $otherMerchant->id,
            'name' => 'Foreign Customer',
            'email' => 'other@customer.com',
        ]);

        // Attempt to ingest usage for other merchant's customer should fail 404
        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/usage/ingest", [
            'customer_id' => $otherCustomer->id,
            'units' => 10,
        ]);

        $response->assertStatus(404);
    }
}

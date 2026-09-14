<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\PlanCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantPlanManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected PlanCacheService $planCacheService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->merchant = Merchant::create([
            'name' => 'Nexus Cloud Platform',
            'slug' => 'nexus-cloud',
            'currency' => 'USD',
        ]);

        $this->planCacheService = app(PlanCacheService::class);
    }

    public function test_merchant_can_define_new_plan_with_all_required_specifications(): void
    {
        $payload = [
            'name' => 'Scale Tier',
            'code' => 'scale-tier',
            'base_price' => 249.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 75000,
            'overage_rate_per_unit' => 0.0075,
            'prorate_allowance' => 1,
            'is_active' => 1,
            'description' => 'High-throughput scaling plan for growing SaaS applications.',
        ];

        $response = $this->post("/merchants/{$this->merchant->id}/plans", $payload);

        $response->assertRedirect("/?merchant_id={$this->merchant->id}&tab=plans");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('plans', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Scale Tier',
            'code' => 'scale-tier',
            'base_price' => 249.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 75000,
            'overage_rate_per_unit' => 0.0075,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        // Verify cache includes the newly created plan
        $cachedPlans = $this->planCacheService->getPlansForMerchant($this->merchant->id);
        $this->assertTrue($cachedPlans->contains('name', 'Scale Tier'));
    }

    public function test_plan_definition_validates_required_fields_and_constraints(): void
    {
        $response = $this->post("/merchants/{$this->merchant->id}/plans", [
            'name' => '',
            'base_price' => -50,
            'billing_cycle' => 'invalid_cycle',
            'included_usage_units' => -100,
            'overage_rate_per_unit' => -0.01,
        ]);

        $response->assertSessionHasErrors([
            'name',
            'base_price',
            'billing_cycle',
            'included_usage_units',
            'overage_rate_per_unit',
        ]);
    }

    public function test_merchant_can_update_existing_plan(): void
    {
        $plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Starter Tier',
            'code' => 'starter',
            'base_price' => 49.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0100,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        // Prime cache
        $this->planCacheService->getPlansForMerchant($this->merchant->id);

        $updatePayload = [
            'name' => 'Starter Tier Pro',
            'base_price' => 59.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 15000,
            'overage_rate_per_unit' => 0.0090,
            'prorate_allowance' => 1,
            'is_active' => 1,
            'description' => 'Updated starter plan with 15k units.',
        ];

        $response = $this->post("/merchants/{$this->merchant->id}/plans/{$plan->id}/update", $updatePayload);

        $response->assertRedirect("/?merchant_id={$this->merchant->id}&tab=plans");
        $response->assertSessionHas('success');

        $plan->refresh();
        $this->assertEquals('Starter Tier Pro', $plan->name);
        $this->assertEquals(59.00, (float) $plan->base_price);
        $this->assertEquals(15000, $plan->included_usage_units);

        // Verify cache was refreshed
        $cachedPlans = $this->planCacheService->getPlansForMerchant($this->merchant->id);
        $this->assertTrue($cachedPlans->contains('name', 'Starter Tier Pro'));
    }

    public function test_merchant_can_toggle_plan_status(): void
    {
        $plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Legacy Plan',
            'code' => 'legacy',
            'base_price' => 29.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 5000,
            'overage_rate_per_unit' => 0.0200,
            'prorate_allowance' => false,
            'is_active' => true,
        ]);

        // Toggle to inactive (archived)
        $response = $this->post("/merchants/{$this->merchant->id}/plans/{$plan->id}/toggle");
        $response->assertRedirect("/?merchant_id={$this->merchant->id}&tab=plans");
        $plan->refresh();
        $this->assertFalse($plan->is_active);

        // Toggle back to active
        $this->post("/merchants/{$this->merchant->id}/plans/{$plan->id}/toggle");
        $plan->refresh();
        $this->assertTrue($plan->is_active);
    }

    public function test_tenant_isolation_prevents_updating_another_merchants_plan(): void
    {
        $otherMerchant = Merchant::create([
            'name' => 'Other Merchant',
            'slug' => 'other-merchant',
            'currency' => 'EUR',
        ]);

        $otherPlan = Plan::create([
            'merchant_id' => $otherMerchant->id,
            'name' => 'Other Plan',
            'code' => 'other-plan',
            'base_price' => 100.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0100,
            'is_active' => true,
        ]);

        $response = $this->post("/merchants/{$this->merchant->id}/plans/{$otherPlan->id}/update", [
            'name' => 'Hacked Plan',
            'base_price' => 1.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 1000,
            'overage_rate_per_unit' => 0.01,
        ]);

        $response->assertStatus(403);
    }

    public function test_newly_defined_plan_is_immediately_available_in_customer_portal(): void
    {
        $customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Customer Corp',
            'email' => 'corp@customer.com',
        ]);

        // Define new plan
        $this->post("/merchants/{$this->merchant->id}/plans", [
            'name' => 'Ultimate Cloud Tier',
            'base_price' => 999.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 500000,
            'overage_rate_per_unit' => 0.0020,
        ]);

        // Visit customer portal
        $response = $this->withSession(['customer_id' => $customer->id])
            ->get('/portal');

        $response->assertStatus(200);
        $response->assertSee('Ultimate Cloud Tier');
        $response->assertSee('500,000');
        $response->assertSee('999.00');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PlanCacheService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantPortalTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Merchant $otherMerchant;
    protected Customer $customer;
    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->merchant = Merchant::create([
            'name' => 'Nexus Cloud Technologies',
            'slug' => 'nexus-cloud',
            'email' => 'billing@nexuscloud.io',
            'currency' => 'USD',
            'timezone' => 'UTC',
        ]);

        $this->otherMerchant = Merchant::create([
            'name' => 'Apex AI Solutions',
            'slug' => 'apex-ai',
            'email' => 'finance@apex-ai.de',
            'currency' => 'EUR',
            'timezone' => 'UTC',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Acme Corp',
            'email' => 'contact@acmecorp.com',
        ]);

        $this->plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Starter Tier',
            'code' => 'starter',
            'base_price' => 49.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 5000,
            'overage_rate_per_unit' => 0.0200,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);
    }

    public function test_merchant_login_page_renders_successfully(): void
    {
        $response = $this->get('/merchant/login');

        $response->assertStatus(200);
        $response->assertSee('Nexus Cloud Technologies');
        $response->assertSee('billing@nexuscloud.io');
        $response->assertSee('Apex AI Solutions');
        $response->assertSee('Sign In to Merchant Portal');
        $response->assertSee('Password (Default: 123456)');
    }

    public function test_merchant_login_with_valid_email(): void
    {
        $response = $this->post('/merchant/login', [
            'email' => 'billing@nexuscloud.io',
        ]);

        $response->assertRedirect('/merchant');
        $response->assertSessionHas('merchant_id', $this->merchant->id);
    }

    public function test_merchant_login_with_merchant_id(): void
    {
        $response = $this->post('/merchant/login', [
            'merchant_id' => $this->merchant->id,
        ]);

        $response->assertRedirect('/merchant');
        $response->assertSessionHas('merchant_id', $this->merchant->id);
    }

    public function test_merchant_login_with_invalid_credentials(): void
    {
        $response = $this->post('/merchant/login', [
            'email' => 'nonexistent@merchant.com',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertNull(session('merchant_id'));
    }

    public function test_unauthenticated_user_redirected_from_merchant_dashboard(): void
    {
        $response = $this->get('/merchant');

        $response->assertRedirect('/merchant/login');
    }

    public function test_authenticated_merchant_can_view_dashboard_with_kpis_and_plans(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->get('/merchant');

        $response->assertStatus(200);
        $response->assertSee('Nexus Cloud Technologies');
        $response->assertSee('Starter Tier');
        $response->assertSee('Define New Plan');
        $response->assertSee('Current Cycle Usage');
        $response->assertSee('Projected Overage Revenue');
        $response->assertSee('Plans Catalog');
        $response->assertSee('Invoices History');
    }

    public function test_authenticated_merchant_dashboard_renders_with_active_subscriptions_and_invoices(): void
    {
        // Create an active subscription
        Subscription::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'starts_at' => Carbon::now()->subDays(10),
            'current_cycle_start' => Carbon::now()->startOfMonth(),
            'current_cycle_end' => Carbon::now()->endOfMonth(),
        ]);

        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->get('/merchant');

        $response->assertStatus(200);
        $response->assertSee('Customer Subscriptions');
        $response->assertSee('Acme Corp');
        $response->assertSee('contact@acmecorp.com');
        $response->assertSee('Base: $');
    }

    public function test_merchant_can_define_new_plan_successfully(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/plans', [
                'name' => 'AI Supercomputer Tier',
                'code' => 'ai-super',
                'base_price' => 499.00,
                'billing_cycle' => 'monthly',
                'cycle_days' => 30,
                'included_usage_units' => 100000,
                'overage_rate_per_unit' => 0.0050,
                'prorate_allowance' => '1',
                'is_active' => '1',
                'description' => 'Ultra high throughput compute tier with dedicated resources.',
            ]);

        $response->assertRedirect('/merchant?tab=plans');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('plans', [
            'merchant_id' => $this->merchant->id,
            'name' => 'AI Supercomputer Tier',
            'code' => 'ai-super',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 100000,
            'overage_rate_per_unit' => 0.0050,
            'is_active' => 1,
        ]);
    }

    public function test_merchant_new_plan_auto_generates_code_slug_if_omitted(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/plans', [
                'name' => 'Enterprise Scale Max',
                'code' => '',
                'base_price' => 799.00,
                'billing_cycle' => 'annual',
                'included_usage_units' => 500000,
                'overage_rate_per_unit' => 0.0025,
            ]);

        $response->assertRedirect('/merchant?tab=plans');

        $this->assertDatabaseHas('plans', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Enterprise Scale Max',
            'code' => 'enterprise-scale-max',
            'cycle_days' => 365,
        ]);
    }

    public function test_merchant_cannot_define_plan_with_invalid_data(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/plans', [
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

    public function test_merchant_can_toggle_plan_status(): void
    {
        $this->assertTrue($this->plan->is_active);

        // Toggle to inactive (archived)
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post("/merchant/plans/{$this->plan->id}/toggle");

        $response->assertRedirect('/merchant?tab=plans');
        $this->assertFalse($this->plan->fresh()->is_active);

        // Toggle back to active
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post("/merchant/plans/{$this->plan->id}/toggle");

        $response->assertRedirect('/merchant?tab=plans');
        $this->assertTrue($this->plan->fresh()->is_active);
    }

    public function test_merchant_cannot_toggle_another_merchants_plan(): void
    {
        $otherPlan = Plan::create([
            'merchant_id' => $this->otherMerchant->id,
            'name' => 'Apex Pro',
            'code' => 'apex-pro',
            'base_price' => 199.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 20000,
            'overage_rate_per_unit' => 0.0100,
            'is_active' => true,
        ]);

        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post("/merchant/plans/{$otherPlan->id}/toggle");

        $response->assertStatus(403);
    }

    public function test_newly_created_plan_is_visible_in_customer_portal(): void
    {
        // 1. Merchant defines a new plan
        $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/plans', [
                'name' => 'Fast Track API Tier',
                'code' => 'fast-track',
                'base_price' => 129.00,
                'billing_cycle' => 'monthly',
                'included_usage_units' => 25000,
                'overage_rate_per_unit' => 0.0150,
            ]);

        // 2. Customer opens Customer Portal
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->get('/portal');

        $response->assertStatus(200);
        $response->assertSee('Fast Track API Tier');
        $response->assertSee('25,000');
    }

    public function test_merchant_logout_clears_session(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/logout');

        $response->assertRedirect('/merchant/login');
        $this->assertNull(session('merchant_id'));
    }

    public function test_merchant_login_with_correct_password_succeeds(): void
    {
        $response = $this->post('/merchant/login', [
            'email' => 'billing@nexuscloud.io',
            'password' => '123456',
        ]);

        $response->assertRedirect('/merchant');
        $response->assertSessionHas('merchant_id', $this->merchant->id);
    }

    public function test_merchant_login_with_incorrect_password_fails(): void
    {
        $response = $this->post('/merchant/login', [
            'email' => 'billing@nexuscloud.io',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertNull(session('merchant_id'));
    }

    public function test_merchant_can_change_password(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/change-password', [
                'current_password' => '123456',
                'password' => 'newsecretpass',
                'password_confirmation' => 'newsecretpass',
            ]);

        $response->assertSessionHas('success');

        // Test login with new password succeeds
        $loginRes = $this->post('/merchant/login', [
            'email' => 'billing@nexuscloud.io',
            'password' => 'newsecretpass',
        ]);
        $loginRes->assertRedirect('/merchant');

        // Test old password now fails
        $failRes = $this->post('/merchant/login', [
            'email' => 'billing@nexuscloud.io',
            'password' => '123456',
        ]);
        $failRes->assertSessionHasErrors(['password']);
    }

    public function test_merchant_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->withSession(['merchant_id' => $this->merchant->id])
            ->post('/merchant/change-password', [
                'current_password' => 'wrongcurrentpass',
                'password' => 'newsecretpass',
                'password_confirmation' => 'newsecretpass',
            ]);

        $response->assertSessionHasErrors(['current_password']);
    }
}

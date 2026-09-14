<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionSegment;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Customer $customer;
    protected Plan $starterPlan;
    protected Plan $growthPlan;
    protected Plan $enterprisePlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->merchant = Merchant::create([
            'name' => 'Acme Cloud Platform',
            'slug' => 'acme-cloud',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Alice Corp',
            'email' => 'alice@example.com',
        ]);

        $this->starterPlan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Starter Tier',
            'code' => 'starter',
            'base_price' => 50.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0100,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $this->growthPlan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Growth Tier',
            'code' => 'growth',
            'base_price' => 150.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 50000,
            'overage_rate_per_unit' => 0.0080,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $this->enterprisePlan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Enterprise Tier',
            'code' => 'enterprise',
            'base_price' => 500.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 200000,
            'overage_rate_per_unit' => 0.0050,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/portal/login');

        $response->assertStatus(200);
        $response->assertSee('Customer Billing Portal');
        $response->assertSee('Alice Corp');
        $response->assertSee('alice@example.com');
    }

    public function test_login_with_valid_email_authenticates_customer(): void
    {
        $response = $this->post('/portal/login', [
            'email' => 'alice@example.com',
        ]);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('customer_id', $this->customer->id);
    }

    public function test_login_with_demo_customer_id(): void
    {
        $response = $this->post('/portal/login', [
            'customer_id' => $this->customer->id,
        ]);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('customer_id', $this->customer->id);
    }

    public function test_unauthenticated_user_redirected_from_portal_dashboard(): void
    {
        $response = $this->get('/portal');

        $response->assertRedirect('/portal/login');
    }

    public function test_authenticated_customer_can_view_dashboard_with_no_subscription(): void
    {
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->get('/portal');

        $response->assertStatus(200);
        $response->assertSee('No Active Subscription');
        $response->assertSee('Starter Tier');
        $response->assertSee('Growth Tier');
        $response->assertSee('Subscribe to Starter Tier');
    }

    public function test_customer_can_purchase_plan_when_unsubscribed(): void
    {
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/purchase', [
                'plan_id' => $this->starterPlan->id,
            ]);

        $response->assertRedirect('/portal');
        $this->assertDatabaseHas('subscriptions', [
            'customer_id' => $this->customer->id,
            'plan_id' => $this->starterPlan->id,
            'status' => 'active',
        ]);
    }

    public function test_cannot_purchase_plan_if_already_active(): void
    {
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribe($this->customer, $this->starterPlan);

        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/purchase', [
                'plan_id' => $this->growthPlan->id,
            ]);

        $response->assertSessionHasErrors('plan');
    }

    public function test_customer_can_upgrade_plan_mid_cycle(): void
    {
        Carbon::setTestNow('2026-09-01 00:00:00');
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);
        $sub = $subscriptionService->subscribe($this->customer, $this->starterPlan);

        // Advance 10 days
        Carbon::setTestNow('2026-09-11 12:00:00');

        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/change-plan', [
                'subscription_id' => $sub->id,
                'plan_id' => $this->growthPlan->id,
            ]);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('success');

        // Check subscription updated
        $sub->refresh();
        $this->assertEquals($this->growthPlan->id, $sub->plan_id);

        // Check subscription segments created (segment 1 for starter plan, segment 2 for growth plan)
        $segments = SubscriptionSegment::where('subscription_id', $sub->id)->orderBy('starts_at')->get();
        $this->assertCount(2, $segments);
        $this->assertEquals($this->starterPlan->id, $segments[0]->plan_id);
        $this->assertEquals($this->growthPlan->id, $segments[1]->plan_id);

        // Verify dashboard displays mid-cycle change
        $dashboardResponse = $this->withSession(['customer_id' => $this->customer->id])
            ->get('/portal');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Mid-Cycle Changed');
        $dashboardResponse->assertSee('Growth Tier');

        Carbon::setTestNow();
    }

    public function test_customer_can_downgrade_plan_mid_cycle(): void
    {
        Carbon::setTestNow('2026-09-01 00:00:00');
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);
        $sub = $subscriptionService->subscribe($this->customer, $this->enterprisePlan);

        // Advance 15 days
        Carbon::setTestNow('2026-09-16 00:00:00');

        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/change-plan', [
                'subscription_id' => $sub->id,
                'plan_id' => $this->starterPlan->id,
            ]);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('success');

        $sub->refresh();
        $this->assertEquals($this->starterPlan->id, $sub->plan_id);

        $segments = SubscriptionSegment::where('subscription_id', $sub->id)->orderBy('starts_at')->get();
        $this->assertCount(2, $segments);
        $this->assertEquals($this->enterprisePlan->id, $segments[0]->plan_id);
        $this->assertEquals($this->starterPlan->id, $segments[1]->plan_id);

        Carbon::setTestNow();
    }

    public function test_customer_logout(): void
    {
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/logout');

        $response->assertRedirect('/portal/login');
        $this->assertFalse(session()->has('customer_id'));
    }

    public function test_customer_login_with_correct_password_succeeds(): void
    {
        $response = $this->post('/portal/login', [
            'email' => 'alice@example.com',
            'password' => '123456',
        ]);

        $response->assertRedirect('/portal');
        $response->assertSessionHas('customer_id', $this->customer->id);
    }

    public function test_customer_login_with_incorrect_password_fails(): void
    {
        $response = $this->post('/portal/login', [
            'email' => 'alice@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertNull(session('customer_id'));
    }

    public function test_customer_can_change_password(): void
    {
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/change-password', [
                'current_password' => '123456',
                'password' => 'newcustomerpass',
                'password_confirmation' => 'newcustomerpass',
            ]);

        $response->assertSessionHas('success');

        // Test login with new password succeeds
        $loginRes = $this->post('/portal/login', [
            'email' => 'alice@example.com',
            'password' => 'newcustomerpass',
        ]);
        $loginRes->assertRedirect('/portal');

        // Test old password now fails
        $failRes = $this->post('/portal/login', [
            'email' => 'alice@example.com',
            'password' => '123456',
        ]);
        $failRes->assertSessionHasErrors(['password']);
    }

    public function test_customer_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->withSession(['customer_id' => $this->customer->id])
            ->post('/portal/change-password', [
                'current_password' => 'wrongcurrentpass',
                'password' => 'newcustomerpass',
                'password_confirmation' => 'newcustomerpass',
            ]);

        $response->assertSessionHasErrors(['current_password']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyUsage;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_dashboard_returns_required_metrics(): void
    {
        $merchant = Merchant::create([
            'name' => 'Metrics Cloud',
            'slug' => 'metrics-cloud',
            'currency' => 'USD',
        ]);

        $plan = Plan::create([
            'merchant_id' => $merchant->id,
            'name' => 'Growth Tier',
            'code' => 'growth',
            'base_price' => 100.00,
            'cycle_days' => 30,
            'included_usage_units' => 1000,
            'overage_rate_per_unit' => 0.05,
        ]);

        // Create 6 customers
        $customers = [];
        for ($i = 1; $i <= 6; $i++) {
            $cust = Customer::create([
                'merchant_id' => $merchant->id,
                'name' => "Customer {$i}",
                'email' => "cust{$i}@example.com",
            ]);
            $customers[] = $cust;

            app(SubscriptionService::class)->subscribe($cust, $plan);
        }

        $now = Carbon::now();
        $thisMonth = $now->format('Y-m-d');
        $lastMonth = $now->copy()->subMonth()->format('Y-m-d');

        // Seed this month's usage for each customer (1: 6000, 2: 5000, 3: 4000, 4: 3000, 5: 2000, 6: 1000)
        foreach ($customers as $idx => $cust) {
            DailyUsage::create([
                'merchant_id' => $merchant->id,
                'customer_id' => $cust->id,
                'metric' => 'api_calls',
                'usage_date' => $thisMonth,
                'total_units' => (6 - $idx) * 1000,
                'event_count' => 10,
            ]);
        }

        // Customer 5 had 10,000 units last month and only 2,000 this month (80% drop -> churn risk!)
        DailyUsage::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customers[4]->id,
            'metric' => 'api_calls',
            'usage_date' => $lastMonth,
            'total_units' => 10000,
            'event_count' => 20,
        ]);

        // Customer 1 had 5,000 last month and 6,000 this month (healthy increase)
        DailyUsage::create([
            'merchant_id' => $merchant->id,
            'customer_id' => $customers[0]->id,
            'metric' => 'api_calls',
            'usage_date' => $lastMonth,
            'total_units' => 5000,
            'event_count' => 15,
        ]);

        // Call Requirement 5 endpoint: GET /merchants/{id}/dashboard
        $response = $this->getJson("/merchants/{$merchant->id}/dashboard");

        $response->assertStatus(200);
        $data = $response->json();

        // 1. Top 5 customers (out of 6)
        $this->assertCount(5, $data['top_customers']);
        $this->assertEquals('Customer 1', $data['top_customers'][0]['name']);
        $this->assertEquals(6000, $data['top_customers'][0]['total_usage_units']);
        $this->assertEquals('Customer 5', $data['top_customers'][4]['name']);

        // 2. Projected overage revenue
        $this->assertArrayHasKey('projected_overage_revenue', $data);
        $this->assertGreaterThan(0, $data['projected_overage_revenue']['projected_overage_amount']);

        // 3. Churn risk customers (>50% drop)
        $this->assertNotEmpty($data['churn_risk_customers']);
        $this->assertEquals('Customer 5', $data['churn_risk_customers'][0]['name']);
        $this->assertEquals(80.0, $data['churn_risk_customers'][0]['drop_percentage']);
    }
}

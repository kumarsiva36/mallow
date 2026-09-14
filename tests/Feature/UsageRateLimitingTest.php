<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyUsage;
use App\Models\Merchant;
use App\Models\UsageEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_usage_is_idempotent_and_does_not_double_count(): void
    {
        $merchant = Merchant::create([
            'name' => 'Throttle Cloud',
            'slug' => 'throttle-cloud',
            'currency' => 'USD',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'name' => 'High Volume Client',
            'email' => 'client@highvol.io',
        ]);

        $payload = [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'units' => 250,
            'metric' => 'api_calls',
            'idempotency_key' => 'idem_key_unique_test_123',
        ];

        // 1. Initial request -> HTTP 201 Created
        $response1 = $this->postJson('/usage', $payload);
        $response1->assertStatus(201);
        $response1->assertJsonPath('status', 'recorded');
        $response1->assertJsonPath('units', 250);

        // Verify 1 event and 250 units in DB
        $this->assertEquals(1, UsageEvent::where('idempotency_key', 'idem_key_unique_test_123')->count());
        $this->assertEquals(250, DailyUsage::where('merchant_id', $merchant->id)->sum('total_units'));

        // 2. Retried request with identical idempotency_key -> HTTP 200 OK (Not Double Counted!)
        $response2 = $this->postJson('/usage', $payload);
        $response2->assertStatus(200);
        $response2->assertJsonPath('status', 'duplicate');

        // Verify still only 1 event and total units still 250
        $this->assertEquals(1, UsageEvent::where('idempotency_key', 'idem_key_unique_test_123')->count());
        $this->assertEquals(250, DailyUsage::where('merchant_id', $merchant->id)->sum('total_units'));
    }

    public function test_usage_endpoint_has_rate_limiting_headers(): void
    {
        $merchant = Merchant::create([
            'name' => 'Header Test Cloud',
            'slug' => 'header-cloud',
            'currency' => 'USD',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'name' => 'Header Client',
            'email' => 'client@headers.io',
        ]);

        $response = $this->postJson('/usage', [
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'units' => 10,
            'metric' => 'api_calls',
        ]);

        $response->assertStatus(201);
        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertEquals(600, (int) $response->headers->get('X-RateLimit-Limit'));
    }
}

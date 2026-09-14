<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DailyUsage;
use App\Models\Merchant;
use App\Models\UsageEvent;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageIngestionTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->merchant = Merchant::create([
            'name' => 'Acme Cloud',
            'slug' => 'acme-cloud',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Widget Corp',
            'email' => 'billing@widget.co',
        ]);
    }

    public function test_single_usage_event_ingestion_and_daily_rollup(): void
    {
        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/usage/ingest", [
            'customer_id' => $this->customer->id,
            'units' => 45,
            'metric' => 'api_calls',
            'idempotency_key' => 'req_test_001',
            'recorded_at' => '2026-09-10 14:30:00',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'recorded',
                'units' => 45,
            ]);

        $this->assertDatabaseHas('usage_events', [
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'units' => 45,
            'idempotency_key' => 'req_test_001',
        ]);

        $this->assertDatabaseHas('daily_usages', [
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'usage_date' => '2026-09-10',
            'total_units' => 45,
            'event_count' => 1,
        ]);
    }

    public function test_idempotency_deduplication_prevents_duplicate_billing(): void
    {
        $payload = [
            'customer_id' => $this->customer->id,
            'units' => 100,
            'metric' => 'api_calls',
            'idempotency_key' => 'idem_key_unique_99',
            'recorded_at' => '2026-09-10 12:00:00',
        ];

        // First attempt -> recorded
        $resp1 = $this->postJson("/api/v1/merchants/{$this->merchant->id}/usage/ingest", $payload);
        $resp1->assertStatus(201)->assertJson(['status' => 'recorded']);

        // Second attempt with exact same key -> duplicate detected
        $resp2 = $this->postJson("/api/v1/merchants/{$this->merchant->id}/usage/ingest", $payload);
        $resp2->assertStatus(200)->assertJson(['status' => 'duplicate']);

        // Check raw table has only 1 event
        $this->assertEquals(1, UsageEvent::where('idempotency_key', 'idem_key_unique_99')->count());

        // Check daily rollup units is 100 (NOT 200!)
        $daily = DailyUsage::where('merchant_id', $this->merchant->id)
            ->where('customer_id', $this->customer->id)
            ->first();

        $this->assertEquals(100, $daily->total_units);
        $this->assertEquals(1, $daily->event_count);
    }

    public function test_batch_ingestion_with_mixed_dates_and_duplicates(): void
    {
        $events = [
            [
                'customer_id' => $this->customer->id,
                'units' => 25,
                'metric' => 'api_calls',
                'idempotency_key' => 'batch_key_1',
                'recorded_at' => '2026-09-08 10:00:00',
            ],
            [
                'customer_id' => $this->customer->id,
                'units' => 35,
                'metric' => 'api_calls',
                'idempotency_key' => 'batch_key_2',
                'recorded_at' => '2026-09-08 11:00:00',
            ],
            [
                'customer_id' => $this->customer->id,
                'units' => 50,
                'metric' => 'api_calls',
                'idempotency_key' => 'batch_key_3',
                'recorded_at' => '2026-09-09 15:00:00',
            ],
            // Duplicate within same payload
            [
                'customer_id' => $this->customer->id,
                'units' => 50,
                'metric' => 'api_calls',
                'idempotency_key' => 'batch_key_3',
                'recorded_at' => '2026-09-09 15:00:00',
            ],
        ];

        $response = $this->postJson("/api/v1/merchants/{$this->merchant->id}/usage/batch", [
            'events' => $events,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'summary' => [
                    'total' => 4,
                    'processed' => 3,
                    'duplicates' => 1,
                    'units_ingested' => 110,
                ]
            ]);

        // Day 2026-09-08 should have 25 + 35 = 60 units (2 events)
        $this->assertDatabaseHas('daily_usages', [
            'customer_id' => $this->customer->id,
            'usage_date' => '2026-09-08',
            'total_units' => 60,
            'event_count' => 2,
        ]);

        // Day 2026-09-09 should have 50 units (1 event, 1 duplicate ignored)
        $this->assertDatabaseHas('daily_usages', [
            'customer_id' => $this->customer->id,
            'usage_date' => '2026-09-09',
            'total_units' => 50,
            'event_count' => 1,
        ]);
    }

    public function test_customer_usage_summary_endpoint(): void
    {
        // Populate 2 days
        DailyUsage::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'usage_date' => '2026-09-01',
            'total_units' => 500,
            'event_count' => 5,
        ]);

        DailyUsage::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'usage_date' => '2026-09-02',
            'total_units' => 700,
            'event_count' => 8,
        ]);

        $response = $this->getJson("/api/v1/merchants/{$this->merchant->id}/customers/{$this->customer->id}/usage?from=2026-09-01&to=2026-09-03");

        $response->assertStatus(200)
            ->assertJson([
                'total_units' => 1200,
                'metric' => 'api_calls',
            ]);
    }
}

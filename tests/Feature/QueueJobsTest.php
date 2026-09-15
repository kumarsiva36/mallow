<?php

namespace Tests\Feature;

use App\Jobs\AggregateCustomerDailyUsageJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\ProcessCycleBillingJob;
use App\Models\Customer;
use App\Models\DailyUsage;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageEvent;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueJobsTest extends TestCase
{
    use RefreshDatabase;

    protected Merchant $merchant;
    protected Customer $customer;
    protected Plan $plan;
    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->merchant = Merchant::create([
            'name' => 'Acme Cloud Services',
            'slug' => 'acme-cloud',
            'currency' => 'USD',
        ]);

        $this->customer = Customer::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Tech Corp',
            'email' => 'tech@acme.corp',
        ]);

        $this->plan = Plan::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Pro Tier',
            'code' => 'pro',
            'base_price' => 100.00,
            'cycle_days' => 30,
            'included_usage_units' => 5000,
            'overage_rate_per_unit' => 0.02,
        ]);

        $now = Carbon::now();
        $this->subscription = app(SubscriptionService::class)->subscribe(
            $this->customer,
            $this->plan,
            $now->copy()->subDays(30),
            $now->copy()->subDays(30)
        );
    }

    public function test_generate_invoice_job_generates_invoice_for_subscription(): void
    {
        $this->assertEquals(0, Invoice::count());

        $job = new GenerateInvoiceJob($this->subscription->id, advanceCycle: true);
        $invoice = $job->handle(app(BillingService::class));

        $this->assertNotNull($invoice);
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(1, Invoice::count());
        $this->assertEquals($this->merchant->id, $invoice->merchant_id);
        $this->assertEquals($this->customer->id, $invoice->customer_id);
        $this->assertEquals(100.00, (float) $invoice->base_amount);
        $this->assertGreaterThan(0, $invoice->items()->count());
    }

    public function test_generate_invoice_endpoint_supports_queue_flag(): void
    {
        Queue::fake();

        $response = $this->postJson(
            "/api/v1/merchants/{$this->merchant->id}/subscriptions/{$this->subscription->id}/generate-invoice?queue=1"
        );

        $response->assertStatus(202)
            ->assertJson([
                'status' => 'queued',
                'subscription_id' => $this->subscription->id,
            ]);

        Queue::assertPushed(GenerateInvoiceJob::class, function ($job) {
            return $job->subscriptionId === $this->subscription->id && $job->advanceCycle === true;
        });
    }

    public function test_process_cycle_endpoint_supports_queue_flag(): void
    {
        Queue::fake();

        $response = $this->postJson(
            "/api/v1/merchants/{$this->merchant->id}/billing/process-cycle?queue=1&chunk=25"
        );

        $response->assertStatus(202)
            ->assertJson([
                'status' => 'queued',
                'merchant_id' => $this->merchant->id,
            ]);

        Queue::assertPushed(ProcessCycleBillingJob::class, function ($job) {
            return $job->merchantId === $this->merchant->id && $job->chunkSize === 25;
        });
    }

    public function test_aggregate_customer_daily_usage_job_aggregates_events(): void
    {
        $targetDate = '2026-09-15';

        // Seed 3 raw usage events for the customer on the target date
        UsageEvent::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'units' => 120,
            'recorded_at' => Carbon::parse("{$targetDate} 09:15:00"),
        ]);

        UsageEvent::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'units' => 280,
            'recorded_at' => Carbon::parse("{$targetDate} 14:30:00"),
        ]);

        UsageEvent::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'units' => 100,
            'recorded_at' => Carbon::parse("{$targetDate} 21:45:00"),
        ]);

        // Event on a different date (should not be counted in this day's aggregate)
        UsageEvent::create([
            'merchant_id' => $this->merchant->id,
            'customer_id' => $this->customer->id,
            'metric' => 'api_calls',
            'units' => 999,
            'recorded_at' => Carbon::parse("2026-09-14 12:00:00"),
        ]);

        // Run the aggregate job
        $job = new AggregateCustomerDailyUsageJob(
            customerId: $this->customer->id,
            date: $targetDate,
            merchantId: $this->merchant->id
        );

        $result = $job->handle(app(UsageService::class));

        $this->assertEquals(1, $result['processed_dates']);
        $this->assertEquals(500, $result['total_units']); // 120 + 280 + 100
        $this->assertEquals(3, $result['event_count']);

        // Verify database daily_usages record
        $daily = DailyUsage::where('merchant_id', $this->merchant->id)
            ->where('customer_id', $this->customer->id)
            ->where('usage_date', $targetDate)
            ->first();

        $this->assertNotNull($daily);
        $this->assertEquals(500, $daily->total_units);
        $this->assertEquals(3, $daily->event_count);
    }

    public function test_aggregate_daily_usage_artisan_command_dispatches_job(): void
    {
        Queue::fake();

        $this->artisan('usage:aggregate-daily', [
            '--customer' => $this->customer->id,
            '--date' => '2026-09-15',
            '--queue' => true,
        ])->assertSuccessful();

        Queue::assertPushed(AggregateCustomerDailyUsageJob::class, function ($job) {
            return $job->customerId === $this->customer->id && $job->date === '2026-09-15';
        });
    }

    public function test_queue_jobs_are_scheduled_daily_at_midnight(): void
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events = collect($schedule->events());

        // Check usage:aggregate-daily scheduled event
        $aggregateEvent = $events->first(function ($event) {
            return str_contains($event->command, 'usage:aggregate-daily') && str_contains($event->command, '--queue');
        });

        $this->assertNotNull($aggregateEvent, 'usage:aggregate-daily --queue is not scheduled');
        $this->assertEquals('0 0 * * *', $aggregateEvent->expression, 'usage:aggregate-daily should run at 12:00 AM (0 0 * * *)');

        // Check billing:process-cycle scheduled event
        $billingEvent = $events->first(function ($event) {
            return str_contains($event->command, 'billing:process-cycle') && str_contains($event->command, '--queue');
        });

        $this->assertNotNull($billingEvent, 'billing:process-cycle --queue is not scheduled');
        $this->assertEquals('0 0 * * *', $billingEvent->expression, 'billing:process-cycle should run at 12:00 AM (0 0 * * *)');
    }
}

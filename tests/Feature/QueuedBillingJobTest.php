<?php

namespace Tests\Feature;

use App\Jobs\ProcessCycleBillingJob;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueuedBillingJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_queued_chunked_job_processes_due_subscriptions(): void
    {
        $merchant = Merchant::create([
            'name' => 'Job Test Cloud',
            'slug' => 'job-cloud',
            'currency' => 'USD',
        ]);

        $plan = Plan::create([
            'merchant_id' => $merchant->id,
            'name' => 'Monthly Tier',
            'code' => 'monthly',
            'base_price' => 50.00,
            'cycle_days' => 30,
            'included_usage_units' => 2000,
            'overage_rate_per_unit' => 0.05,
        ]);

        $now = Carbon::now();
        $cycleStart = $now->copy()->subDays(30);
        $cycleEnd = $now->copy()->subMinutes(10); // Due!

        // Create 3 customers with due subscriptions
        for ($i = 1; $i <= 3; $i++) {
            $cust = Customer::create([
                'merchant_id' => $merchant->id,
                'name' => "Batch Customer {$i}",
                'email' => "batch{$i}@cloud.io",
            ]);

            $sub = app(SubscriptionService::class)->subscribe($cust, $plan, $cycleStart, $cycleStart);
            $sub->update(['current_cycle_end' => $cycleEnd]);
            $sub->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);
        }

        $this->assertEquals(0, Invoice::count());

        // Execute the chunked queued job directly
        $job = new ProcessCycleBillingJob(
            merchantId: $merchant->id,
            asOf: $now->toDateTimeString(),
            chunkSize: 2 // Verify chunking with chunk size = 2
        );

        $processedCount = $job->handle(app(BillingService::class));

        // 3 invoices should be generated and cycle advanced
        $this->assertEquals(3, $processedCount);
        $this->assertEquals(3, Invoice::where('merchant_id', $merchant->id)->count());
    }

    public function test_artisan_command_can_dispatch_job_to_queue(): void
    {
        Queue::fake();

        $this->artisan('billing:process-cycle', [
            '--queue' => true,
            '--chunk' => 50,
        ])->assertSuccessful();

        Queue::assertPushed(ProcessCycleBillingJob::class, function ($job) {
            return $job->chunkSize === 50;
        });
    }
}

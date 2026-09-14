<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCycleBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $merchantId = null,
        public ?string $asOf = null,
        public int $chunkSize = 100
    ) {}

    /**
     * Execute the job: process due subscriptions in chunked batches.
     */
    public function handle(BillingService $billingService): int
    {
        $asOfDate = $this->asOf ? Carbon::parse($this->asOf) : Carbon::now();

        $query = Subscription::query()
            ->where('status', 'active')
            ->where('current_cycle_end', '<=', $asOfDate);

        if ($this->merchantId !== null) {
            $query->where('merchant_id', $this->merchantId);
        }

        $processedCount = 0;

        $query->chunkById($this->chunkSize, function ($subscriptions) use ($billingService, &$processedCount) {
            foreach ($subscriptions as $subscription) {
                try {
                    $invoice = $billingService->generateInvoice($subscription, advanceCycle: true);
                    $processedCount++;
                    Log::info("Generated invoice {$invoice->invoice_number} for subscription #{$subscription->id}");
                } catch (\Throwable $e) {
                    Log::error("Failed to generate cycle invoice for subscription #{$subscription->id}: " . $e->getMessage());
                }
            }
        });

        return $processedCount;
    }
}

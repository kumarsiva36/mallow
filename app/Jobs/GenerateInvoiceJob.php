<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $subscriptionId;
    public bool $advanceCycle;
    public string $status;

    public function __construct(
        Subscription|int $subscription,
        bool $advanceCycle = true,
        string $status = 'issued'
    ) {
        $this->subscriptionId = $subscription instanceof Subscription ? $subscription->id : $subscription;
        $this->advanceCycle = $advanceCycle;
        $this->status = $status;
    }

    /**
     * Execute the job: generate and persist invoice for the subscription.
     */
    public function handle(BillingService $billingService): ?Invoice
    {
        $subscription = Subscription::with(['merchant', 'customer', 'plan'])->find($this->subscriptionId);

        if (!$subscription) {
            Log::warning("GenerateInvoiceJob skipped: Subscription #{$this->subscriptionId} not found.");
            return null;
        }

        try {
            $invoice = $billingService->generateInvoice(
                $subscription,
                $this->advanceCycle,
                $this->status
            );

            Log::info("GenerateInvoiceJob completed: Generated invoice #{$invoice->invoice_number} for subscription #{$this->subscriptionId}");

            return $invoice;
        } catch (\Throwable $e) {
            Log::error("GenerateInvoiceJob failed for subscription #{$this->subscriptionId}: " . $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCycleBillingJob;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessBillingCycleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:process-cycle
                            {--merchant= : Optional Merchant ID to process only a specific tenant}
                            {--as-of= : Simulated current date (YYYY-MM-DD HH:MM:SS) for testing}
                            {--queue : Dispatch as an asynchronous queued chunked job}
                            {--chunk=100 : Chunk size for processing subscriptions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process cycle-end billing and generate invoices for all due subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(BillingService $billingService): int
    {
        $merchantId = $this->option('merchant') ? (int) $this->option('merchant') : null;
        $asOfStr = $this->option('as-of');
        $asOf = $asOfStr ? Carbon::parse($asOfStr) : Carbon::now();
        $chunkSize = (int) $this->option('chunk');

        if ($this->option('queue')) {
            ProcessCycleBillingJob::dispatch($merchantId, $asOf->toDateTimeString(), $chunkSize);
            $this->info("Dispatched queued chunked billing job for subscriptions due as of: {$asOf->toDateTimeString()}");
            return Command::SUCCESS;
        }

        $this->info("Evaluating subscriptions due for billing as of: {$asOf->toDateTimeString()}");
        if ($merchantId) {
            $this->comment("Scoped to Merchant ID: {$merchantId}");
        }

        $invoices = $billingService->processDueSubscriptions($merchantId, $asOf);

        if ($invoices->isEmpty()) {
            $this->info("No subscriptions currently due for cycle-end billing.");
            return Command::SUCCESS;
        }

        $this->info("Successfully processed {$invoices->count()} subscription(s). Invoices generated:");

        $tableRows = [];
        foreach ($invoices as $inv) {
            $tableRows[] = [
                $inv->invoice_number,
                $inv->merchant->name ?? $inv->merchant_id,
                $inv->customer->name ?? $inv->customer_id,
                $inv->period_start->format('Y-m-d') . ' to ' . $inv->period_end->format('Y-m-d'),
                sprintf('%.1f%%', $inv->proration_ratio * 100),
                "{$inv->currency} " . number_format($inv->base_amount, 2),
                "{$inv->currency} " . number_format($inv->overage_amount, 2),
                "{$inv->currency} " . number_format($inv->total_amount, 2),
            ];
        }

        $this->table(
            ['Invoice #', 'Merchant', 'Customer', 'Period', 'Proration', 'Base Fee', 'Overage Fee', 'Total'],
            $tableRows
        );

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\AggregateCustomerDailyUsageJob;
use App\Models\Customer;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AggregateDailyUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usage:aggregate-daily
                            {--customer= : ID of a specific customer to aggregate}
                            {--merchant= : ID of a specific merchant to scope aggregation}
                            {--date= : Specific date (YYYY-MM-DD) to aggregate (defaults to today)}
                            {--queue : Dispatch as asynchronous queued job(s)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate raw usage events into customer daily usage rollups';

    /**
     * Execute the console command.
     */
    public function handle(UsageService $usageService): int
    {
        $customerId = $this->option('customer') ? (int) $this->option('customer') : null;
        $merchantId = $this->option('merchant') ? (int) $this->option('merchant') : null;
        $rawDate = $this->option('date');
        $dateStr = $rawDate === 'yesterday'
            ? Carbon::yesterday()->toDateString()
            : ($rawDate ? Carbon::parse($rawDate)->toDateString() : Carbon::today()->toDateString());
        $isQueued = (bool) $this->option('queue');

        $this->info("Aggregating daily usage for date: {$dateStr}");

        if ($customerId !== null) {
            $customer = Customer::find($customerId);
            if (!$customer) {
                $this->error("Customer #{$customerId} not found.");
                return Command::FAILURE;
            }

            if ($isQueued) {
                AggregateCustomerDailyUsageJob::dispatch($customerId, $dateStr, $merchantId ?? $customer->merchant_id);
                $this->info("Dispatched queued aggregation job for Customer #{$customerId}");
                return Command::SUCCESS;
            }

            $result = $usageService->aggregateCustomerDailyUsage($customerId, $dateStr, $merchantId ?? $customer->merchant_id);
            $this->info("Aggregated Customer #{$customerId}: {$result['total_units']} units from {$result['event_count']} event(s).");
            return Command::SUCCESS;
        }

        // Aggregate for all customers (optionally scoped by merchant)
        $query = Customer::query();
        if ($merchantId !== null) {
            $query->where('merchant_id', $merchantId);
        }

        $customers = $query->get(['id', 'merchant_id', 'name']);
        if ($customers->isEmpty()) {
            $this->info("No customers found to aggregate.");
            return Command::SUCCESS;
        }

        $this->info("Processing daily usage aggregation for {$customers->count()} customer(s)...");

        $totalUnitsAll = 0;
        $totalEventsAll = 0;

        foreach ($customers as $cust) {
            if ($isQueued) {
                AggregateCustomerDailyUsageJob::dispatch($cust->id, $dateStr, $cust->merchant_id);
            } else {
                $res = $usageService->aggregateCustomerDailyUsage($cust->id, $dateStr, $cust->merchant_id);
                $totalUnitsAll += $res['total_units'];
                $totalEventsAll += $res['event_count'];
            }
        }

        if ($isQueued) {
            $this->info("Dispatched {$customers->count()} queued aggregation job(s).");
        } else {
            $this->info("Daily aggregation complete! Aggregated {$totalUnitsAll} units from {$totalEventsAll} event(s) across {$customers->count()} customer(s).");
        }

        return Command::SUCCESS;
    }
}

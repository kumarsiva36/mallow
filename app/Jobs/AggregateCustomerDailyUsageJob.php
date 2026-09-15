<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\UsageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AggregateCustomerDailyUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $customerId,
        public ?string $date = null,
        public ?int $merchantId = null,
        public string $metric = 'api_calls'
    ) {}

    /**
     * Execute the job: aggregate raw usage events into the daily_usages rollup table.
     */
    public function handle(UsageService $usageService): array
    {
        $customer = Customer::find($this->customerId);

        if (!$customer) {
            Log::warning("AggregateCustomerDailyUsageJob skipped: Customer #{$this->customerId} not found.");
            return [
                'status' => 'skipped',
                'message' => "Customer #{$this->customerId} not found.",
            ];
        }

        $merchantId = $this->merchantId ?? $customer->merchant_id;

        try {
            $result = $usageService->aggregateCustomerDailyUsage(
                $this->customerId,
                $this->date,
                $merchantId,
                $this->metric
            );

            Log::info(sprintf(
                "AggregateCustomerDailyUsageJob completed for Customer #%d (Merchant #%d): %d dates processed, %d units from %d events.",
                $this->customerId,
                $merchantId,
                $result['processed_dates'],
                $result['total_units'],
                $result['event_count']
            ));

            return $result;
        } catch (\Throwable $e) {
            Log::error("AggregateCustomerDailyUsageJob failed for Customer #{$this->customerId}: " . $e->getMessage());
            throw $e;
        }
    }
}

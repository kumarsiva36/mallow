<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Merchant;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SimulateUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usage:simulate
                            {--merchant= : Target Merchant ID (defaults to first)}
                            {--customer= : Target Customer ID (defaults to first)}
                            {--days=15 : Number of days to simulate backwards from today}
                            {--events-per-day=20 : Number of events per day}
                            {--units-per-event=50 : Base units per event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate high-volume usage events to test batch ingestion and billing overages';

    /**
     * Execute the console command.
     */
    public function handle(UsageService $usageService): int
    {
        $merchantId = $this->option('merchant')
            ? (int) $this->option('merchant')
            : Merchant::first()?->id;

        if (!$merchantId) {
            $this->error("No merchant found. Please run db:seed first or specify --merchant");
            return Command::FAILURE;
        }

        $customerId = $this->option('customer')
            ? (int) $this->option('customer')
            : Customer::where('merchant_id', $merchantId)->first()?->id;

        if (!$customerId) {
            $this->error("No customer found for merchant {$merchantId}.");
            return Command::FAILURE;
        }

        $days = (int) $this->option('days');
        $eventsPerDay = (int) $this->option('events-per-day');
        $unitsPerEvent = (int) $this->option('units-per-event');

        $this->info("Simulating usage for Merchant {$merchantId}, Customer {$customerId}:");
        $this->comment("Days: {$days}, Events/Day: {$eventsPerDay}, Units/Event: {$unitsPerEvent}");

        $eventsBatch = [];
        $totalUnits = 0;
        $now = Carbon::now();

        for ($d = $days; $d >= 0; $d--) {
            $date = $now->copy()->subDays($d);
            for ($e = 0; $e < $eventsPerDay; $e++) {
                $variance = rand(-15, 25);
                $units = max(1, $unitsPerEvent + $variance);
                $recordedAt = $date->copy()->addMinutes($e * (1440 / max(1, $eventsPerDay)));
                
                $eventsBatch[] = [
                    'customer_id' => $customerId,
                    'metric' => 'api_calls',
                    'units' => $units,
                    'idempotency_key' => 'sim_' . Str::random(16),
                    'recorded_at' => $recordedAt->toDateTimeString(),
                ];

                $totalUnits += $units;
            }
        }

        $this->comment("Ingesting batch of " . count($eventsBatch) . " events...");
        $startTime = microtime(true);
        $summary = $usageService->recordBatch($merchantId, $eventsBatch);
        $elapsed = round(microtime(true) - $startTime, 3);

        $this->info("Batch Ingestion Complete in {$elapsed}s!");
        $this->table(
            ['Total Events', 'Processed', 'Duplicates Filtered', 'Total Units Ingested'],
            [[$summary['total'], $summary['processed'], $summary['duplicates'], number_format($summary['units_ingested'])]]
        );

        return Command::SUCCESS;
    }
}

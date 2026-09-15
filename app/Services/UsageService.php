<?php

namespace App\Services;

use App\Models\DailyUsage;
use App\Models\UsageEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UsageService
{
    /**
     * Record a single usage event idempotently.
     */
    public function recordEvent(
        int $merchantId,
        int $customerId,
        int $units,
        string $metric = 'api_calls',
        ?string $idempotencyKey = null,
        ?Carbon $recordedAt = null
    ): array {
        $recordedAt = $recordedAt ?? Carbon::now();
        $usageDate = $recordedAt->toDateString();

        return DB::transaction(function () use ($merchantId, $customerId, $units, $metric, $idempotencyKey, $recordedAt, $usageDate) {
            if ($idempotencyKey !== null) {
                $existing = UsageEvent::where('merchant_id', $merchantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    return [
                        'status' => 'duplicate',
                        'message' => 'Event already recorded with this idempotency key.',
                        'event_id' => $existing->id,
                        'units' => $existing->units,
                    ];
                }
            }

            $event = UsageEvent::create([
                'merchant_id' => $merchantId,
                'customer_id' => $customerId,
                'metric' => $metric,
                'units' => $units,
                'idempotency_key' => $idempotencyKey,
                'recorded_at' => $recordedAt,
            ]);

            // Update daily rollup atomically using ON CONFLICT DO UPDATE
            $this->applyDailyRollup(
                $merchantId,
                $customerId,
                $metric,
                $usageDate,
                $units,
                1,
                $recordedAt
            );

            return [
                'status' => 'recorded',
                'event_id' => $event->id,
                'units' => $units,
                'recorded_at' => $recordedAt->toIso8601String(),
            ];
        });
    }

    /**
     * Ingest a batch of usage events efficiently.
     * Deduplicates by idempotency_key and aggregates into daily rollups.
     */
    public function recordBatch(int $merchantId, array $events): array
    {
        if (empty($events)) {
            return [
                'total' => 0,
                'processed' => 0,
                'duplicates' => 0,
                'units_ingested' => 0,
            ];
        }

        return DB::transaction(function () use ($merchantId, $events) {
            $idempotencyKeys = array_filter(array_column($events, 'idempotency_key'));
            $existingKeys = [];

            if (!empty($idempotencyKeys)) {
                $existingKeys = UsageEvent::where('merchant_id', $merchantId)
                    ->whereIn('idempotency_key', $idempotencyKeys)
                    ->pluck('idempotency_key')
                    ->all();
                $existingKeys = array_flip($existingKeys);
            }

            $eventsToInsert = [];
            $aggregates = []; // key: customer_id:metric:date
            $processedCount = 0;
            $duplicateCount = 0;
            $totalUnits = 0;
            $now = Carbon::now();

            foreach ($events as $event) {
                $key = $event['idempotency_key'] ?? null;
                if ($key !== null && isset($existingKeys[$key])) {
                    $duplicateCount++;
                    continue;
                }

                // If key is seen multiple times within this batch
                if ($key !== null) {
                    $existingKeys[$key] = true;
                }

                $customerId = (int)$event['customer_id'];
                $metric = $event['metric'] ?? 'api_calls';
                $units = (int)$event['units'];
                $recordedAt = isset($event['recorded_at']) ? Carbon::parse($event['recorded_at']) : $now;
                $dateStr = $recordedAt->toDateString();

                $eventsToInsert[] = [
                    'merchant_id' => $merchantId,
                    'customer_id' => $customerId,
                    'metric' => $metric,
                    'units' => $units,
                    'idempotency_key' => $key,
                    'recorded_at' => $recordedAt,
                    'created_at' => $now,
                ];

                $aggKey = "{$customerId}:{$metric}:{$dateStr}";
                if (!isset($aggregates[$aggKey])) {
                    $aggregates[$aggKey] = [
                        'customer_id' => $customerId,
                        'metric' => $metric,
                        'usage_date' => $dateStr,
                        'units' => 0,
                        'count' => 0,
                        'last_recorded_at' => $recordedAt,
                    ];
                }

                $aggregates[$aggKey]['units'] += $units;
                $aggregates[$aggKey]['count'] += 1;
                if ($recordedAt->gt($aggregates[$aggKey]['last_recorded_at'])) {
                    $aggregates[$aggKey]['last_recorded_at'] = $recordedAt;
                }

                $processedCount++;
                $totalUnits += $units;
            }

            // Chunked insert into raw events table
            foreach (array_chunk($eventsToInsert, 500) as $chunk) {
                UsageEvent::insert($chunk);
            }

            // Apply rollups to daily_usages atomically
            foreach ($aggregates as $agg) {
                $this->applyDailyRollup(
                    $merchantId,
                    $agg['customer_id'],
                    $agg['metric'],
                    $agg['usage_date'],
                    $agg['units'],
                    $agg['count'],
                    $agg['last_recorded_at']
                );
            }

            return [
                'total' => count($events),
                'processed' => $processedCount,
                'duplicates' => $duplicateCount,
                'units_ingested' => $totalUnits,
            ];
        });
    }

    /**
     * Atomically upsert daily usage rollups using database native ON CONFLICT.
     */
    public function applyDailyRollup(
        int $merchantId,
        int $customerId,
        string $metric,
        string $usageDate,
        int $units,
        int $eventCount,
        Carbon $lastRecordedAt
    ): void {
        $now = Carbon::now()->toDateTimeString();
        $recordedAtStr = $lastRecordedAt->toDateTimeString();
        $dateStr = Carbon::parse($usageDate)->toDateString();

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement(
                'INSERT INTO daily_usages (merchant_id, customer_id, metric, usage_date, total_units, event_count, last_recorded_at, created_at, updated_at) ' .
                'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ' .
                'ON DUPLICATE KEY UPDATE ' .
                'total_units = total_units + VALUES(total_units), ' .
                'event_count = event_count + VALUES(event_count), ' .
                'last_recorded_at = VALUES(last_recorded_at), ' .
                'updated_at = VALUES(updated_at)',
                [
                    $merchantId,
                    $customerId,
                    $metric,
                    $dateStr,
                    $units,
                    $eventCount,
                    $recordedAtStr,
                    $now,
                    $now,
                ]
            );
        } else {
            DB::statement(
                'INSERT INTO daily_usages (merchant_id, customer_id, metric, usage_date, total_units, event_count, last_recorded_at, created_at, updated_at) ' .
                'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ' .
                'ON CONFLICT(merchant_id, customer_id, metric, usage_date) DO UPDATE SET ' .
                'total_units = daily_usages.total_units + excluded.total_units, ' .
                'event_count = daily_usages.event_count + excluded.event_count, ' .
                'last_recorded_at = excluded.last_recorded_at, ' .
                'updated_at = excluded.updated_at',
                [
                    $merchantId,
                    $customerId,
                    $metric,
                    $dateStr,
                    $units,
                    $eventCount,
                    $recordedAtStr,
                    $now,
                    $now,
                ]
            );
        }
    }

    public function getUsageForPeriod(
        int $merchantId,
        int $customerId,
        Carbon $from,
        Carbon $to,
        string $metric = 'api_calls'
    ): int {
        // If sub-day granularity is present, check raw usage_events for high precision
        $hasTimeComponent = ($from->format('H:i:s') !== '00:00:00') ||
                            ($to->format('H:i:s') !== '23:59:59' && $to->format('H:i:s') !== '00:00:00');

        if ($hasTimeComponent) {
            $rawSum = UsageEvent::where('merchant_id', $merchantId)
                ->where('customer_id', $customerId)
                ->where('metric', $metric)
                ->where('recorded_at', '>=', $from)
                ->where('recorded_at', '<=', $to)
                ->sum('units');

            if ($rawSum > 0 || UsageEvent::where('merchant_id', $merchantId)->where('customer_id', $customerId)->exists()) {
                return (int) $rawSum;
            }
        }

        return (int) DailyUsage::where('merchant_id', $merchantId)
            ->where('customer_id', $customerId)
            ->where('metric', $metric)
            ->whereBetween('usage_date', [$from->toDateString(), $to->toDateString()])
            ->sum('total_units');
    }

    /**
     * Get daily breakdown for customer within date range.
     */
    public function getDailyBreakdown(
        int $merchantId,
        int $customerId,
        Carbon $from,
        Carbon $to,
        string $metric = 'api_calls'
    ): Collection {
        return DailyUsage::where('merchant_id', $merchantId)
            ->where('customer_id', $customerId)
            ->where('metric', $metric)
            ->whereBetween('usage_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('usage_date')
            ->get();
    }

    /**
     * Aggregate raw usage events for a customer on a given date (or all dates) into daily_usages.
     */
    public function aggregateCustomerDailyUsage(
        int $customerId,
        ?string $date = null,
        ?int $merchantId = null,
        string $metric = 'api_calls'
    ): array {
        if ($merchantId === null) {
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return ['processed_dates' => 0, 'total_units' => 0, 'event_count' => 0];
            }
            $merchantId = $customer->merchant_id;
        }

        $query = UsageEvent::where('merchant_id', $merchantId)
            ->where('customer_id', $customerId)
            ->where('metric', $metric);

        if ($date !== null) {
            $dateCarbon = Carbon::parse($date);
            $query->whereBetween('recorded_at', [
                $dateCarbon->copy()->startOfDay(),
                $dateCarbon->copy()->endOfDay(),
            ]);
        }

        $driver = DB::connection()->getDriverName();
        $dateExpr = in_array($driver, ['sqlite'])
            ? "strftime('%Y-%m-%d', recorded_at)"
            : "DATE(recorded_at)";

        $dailyStats = $query->selectRaw("
            {$dateExpr} as usage_date,
            SUM(units) as total_units,
            COUNT(*) as event_count,
            MAX(recorded_at) as last_recorded_at
        ")
        ->groupBy(DB::raw($dateExpr))
        ->get();

        $processedDates = 0;
        $totalAggUnits = 0;
        $totalAggEvents = 0;

        foreach ($dailyStats as $stat) {
            $usageDateStr = (string) $stat->usage_date;
            $units = (int) $stat->total_units;
            $eventCount = (int) $stat->event_count;
            $lastRecorded = $stat->last_recorded_at ? Carbon::parse($stat->last_recorded_at) : Carbon::now();

            DailyUsage::updateOrCreate(
                [
                    'merchant_id' => $merchantId,
                    'customer_id' => $customerId,
                    'metric' => $metric,
                    'usage_date' => $usageDateStr,
                ],
                [
                    'total_units' => $units,
                    'event_count' => $eventCount,
                    'last_recorded_at' => $lastRecorded,
                ]
            );

            $processedDates++;
            $totalAggUnits += $units;
            $totalAggEvents += $eventCount;
        }

        return [
            'merchant_id' => $merchantId,
            'customer_id' => $customerId,
            'metric' => $metric,
            'processed_dates' => $processedDates,
            'total_units' => $totalAggUnits,
            'event_count' => $totalAggEvents,
        ];
    }
}

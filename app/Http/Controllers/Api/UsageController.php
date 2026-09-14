<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Merchant;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function __construct(
        protected UsageService $usageService
    ) {}

    /**
     * Record a usage event directly via POST /usage or POST /v1/usage.
     * High-throughput, idempotent, and rate-limited.
     */
    public function ingestDirect(Request $request): JsonResponse
    {
        if (!$request->has('merchant_id') && $request->hasHeader('X-Merchant-Id')) {
            $request->merge(['merchant_id' => $request->header('X-Merchant-Id')]);
        }

        $validated = $request->validate([
            'merchant_id' => 'required|integer|exists:merchants,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'units' => 'required|integer|min:1',
            'metric' => 'nullable|string|max:50',
            'idempotency_key' => 'nullable|string|max:128',
            'recorded_at' => 'nullable|date',
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);
        $customer = Customer::where('merchant_id', $merchant->id)->findOrFail($validated['customer_id']);

        $recordedAt = isset($validated['recorded_at']) ? Carbon::parse($validated['recorded_at']) : Carbon::now();
        $metric = $validated['metric'] ?? 'api_calls';
        $key = $validated['idempotency_key'] ?? null;

        $result = $this->usageService->recordEvent(
            $merchant->id,
            $customer->id,
            $validated['units'],
            $metric,
            $key,
            $recordedAt
        );

        $statusCode = ($result['status'] === 'duplicate') ? 200 : 201;

        return response()->json($result, $statusCode);
    }

    /**
     * Record a single usage event (e.g. API call) scoped to a merchant.
     */
    public function ingest(Request $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'units' => 'required|integer|min:1',
            'metric' => 'nullable|string|max:50',
            'idempotency_key' => 'nullable|string|max:128',
            'recorded_at' => 'nullable|date',
        ]);

        // Ensure customer belongs to this merchant
        $customer = Customer::where('merchant_id', $merchant->id)->findOrFail($validated['customer_id']);

       // dd($customer);

        $recordedAt = isset($validated['recorded_at']) ? Carbon::parse($validated['recorded_at']) : Carbon::now();
        $metric = $validated['metric'] ?? 'api_calls';
        $key = $validated['idempotency_key'] ?? null;

        $result = $this->usageService->recordEvent(
            $merchant->id,
            $customer->id,
            $validated['units'],
            $metric,
            $key,
            $recordedAt
        );

        $statusCode = ($result['status'] === 'duplicate') ? 200 : 201;

        return response()->json($result, $statusCode);
    }

    /**
     * Ingest a batch of usage events (for high write-volume pipelines).
     */
    public function batch(Request $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validate([
            'events' => 'required|array|min:1|max:10000',
            'events.*.customer_id' => 'required|integer|exists:customers,id',
            'events.*.units' => 'required|integer|min:1',
            'events.*.metric' => 'nullable|string|max:50',
            'events.*.idempotency_key' => 'nullable|string|max:128',
            'events.*.recorded_at' => 'nullable|date',
        ]);

        $result = $this->usageService->recordBatch($merchant->id, $validated['events']);

        return response()->json([
            'message' => 'Batch usage ingestion completed',
            'summary' => $result,
        ], 200);
    }

    /**
     * Retrieve usage summary & daily breakdown for a customer.
     */
    public function summary(Request $request, Merchant $merchant, Customer $customer): JsonResponse
    {
        if ($customer->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Customer does not belong to this merchant'], 404);
        }

        $from = $request->query('from') ? Carbon::parse($request->query('from')) : Carbon::now()->subDays(30);
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : Carbon::now();
        $metric = $request->query('metric', 'api_calls');

        $totalUnits = $this->usageService->getUsageForPeriod($merchant->id, $customer->id, $from, $to, $metric);
        $breakdown = $this->usageService->getDailyBreakdown($merchant->id, $customer->id, $from, $to, $metric);

        return response()->json([
            'customer_id' => $customer->id,
            'merchant_id' => $merchant->id,
            'metric' => $metric,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_units' => $totalUnits,
            'daily_breakdown' => $breakdown,
        ]);
    }
}

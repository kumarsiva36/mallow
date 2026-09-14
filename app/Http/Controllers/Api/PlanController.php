<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\PlanCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function __construct(
        protected PlanCacheService $planCacheService
    ) {}

    public function index(Merchant $merchant): JsonResponse
    {
        $plans = $this->planCacheService->getPlansForMerchant($merchant->id);
        return response()->json(['data' => $plans]);
    }

    public function store(Request $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'billing_cycle' => 'nullable|string|in:monthly,annual',
            'cycle_days' => 'nullable|integer|min:1|max:365',
            'included_usage_units' => 'required|integer|min:0',
            'overage_rate_per_unit' => 'required|numeric|min:0',
            'prorate_allowance' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = Str::slug($validated['name']);
        }

        $validated['merchant_id'] = $merchant->id;

        $plan = Plan::create($validated);

        return response()->json([
            'message' => 'Plan created successfully',
            'data' => $plan,
        ], 201);
    }

    public function show(Merchant $merchant, Plan $plan): JsonResponse
    {
        if ($plan->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Plan does not belong to this merchant'], 404);
        }

        return response()->json(['data' => $plan]);
    }
}

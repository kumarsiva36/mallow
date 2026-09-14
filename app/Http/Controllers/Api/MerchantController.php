<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Services\DashboardMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index(): JsonResponse
    {
        $merchants = Merchant::withCount(['plans', 'customers', 'subscriptions'])->get();
        return response()->json(['data' => $merchants]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:merchants,slug',
            'email' => 'nullable|email|max:255',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'nullable|string|max:50',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }

        $merchant = Merchant::create($validated);

        return response()->json([
            'message' => 'Merchant created successfully',
            'data' => $merchant,
        ], 201);
    }

    public function show(Merchant $merchant): JsonResponse
    {
        return response()->json([
            'data' => $merchant->load(['plans', 'customers.activeSubscription.plan'])
        ]);
    }

    /**
     * Requirement 5: GET /merchants/{id}/dashboard
     * Returns top 5 customers, projected overage revenue, and churn risk customers.
     */
    public function dashboard(Merchant $merchant, DashboardMetricsService $metricsService): JsonResponse
    {
        $metrics = $metricsService->getMetrics($merchant);
        return response()->json($metrics);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected BillingService $billingService
    ) {}

    public function index(Merchant $merchant): JsonResponse
    {
        $subscriptions = $merchant->subscriptions()
            ->with(['customer', 'plan'])
            ->latest()
            ->get();

        return response()->json(['data' => $subscriptions]);
    }

    public function store(Request $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'nullable|date',
            'cycle_start' => 'nullable|date',
        ]);

        $customer = Customer::where('merchant_id', $merchant->id)->findOrFail($validated['customer_id']);
        $plan = Plan::where('merchant_id', $merchant->id)->findOrFail($validated['plan_id']);

        $startsAt = isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : Carbon::now();
        $cycleStart = isset($validated['cycle_start']) ? Carbon::parse($validated['cycle_start']) : null;

        $subscription = $this->subscriptionService->subscribe($customer, $plan, $startsAt, $cycleStart);

        return response()->json([
            'message' => 'Subscription created successfully',
            'data' => $subscription->load(['customer', 'plan']),
        ], 201);
    }

    public function show(Merchant $merchant, Subscription $subscription): JsonResponse
    {
        if ($subscription->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Subscription does not belong to this merchant'], 404);
        }

        $billingPreview = $this->billingService->calculateBillingDetails($subscription);

        return response()->json([
            'data' => $subscription->load(['customer', 'plan', 'invoices']),
            'billing_preview' => $billingPreview,
        ]);
    }

    public function cancel(Request $request, Merchant $merchant, Subscription $subscription): JsonResponse
    {
        if ($subscription->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Subscription does not belong to this merchant'], 404);
        }

        $immediately = $request->boolean('immediately', false);
        $updated = $this->subscriptionService->cancel($subscription, $immediately);

        return response()->json([
            'message' => 'Subscription cancelled successfully',
            'data' => $updated,
        ]);
    }

    /**
     * Change plan mid-cycle (upgrade/downgrade) with automatic segment tracking.
     */
    public function changePlan(Request $request, Merchant $merchant, Subscription $subscription): JsonResponse
    {
        if ($subscription->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Subscription does not belong to this merchant'], 404);
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'effective_at' => 'nullable|date',
        ]);

        $newPlan = Plan::where('merchant_id', $merchant->id)->findOrFail($validated['plan_id']);
        $effectiveAt = isset($validated['effective_at']) ? Carbon::parse($validated['effective_at']) : Carbon::now();

        $updated = $this->subscriptionService->changePlan($subscription, $newPlan, $effectiveAt);

        return response()->json([
            'message' => 'Subscription plan changed successfully',
            'data' => $updated->load(['customer', 'plan', 'segments']),
            'billing_preview' => $this->billingService->calculateBillingDetails($updated),
        ]);
    }
}

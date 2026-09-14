<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Preview billing calculation for a subscription before cycle end.
     */
    public function preview(Merchant $merchant, Subscription $subscription): JsonResponse
    {
        if ($subscription->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Subscription does not belong to this merchant'], 404);
        }

        $details = $this->billingService->calculateBillingDetails($subscription);

        return response()->json(['data' => $details]);
    }

    /**
     * Generate cycle-end invoice for a single subscription.
     */
    public function generate(Request $request, Merchant $merchant, Subscription $subscription): JsonResponse
    {
        if ($subscription->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Subscription does not belong to this merchant'], 404);
        }

        $advanceCycle = $request->boolean('advance_cycle', true);
        $status = $request->input('status', 'issued');

        $invoice = $this->billingService->generateInvoice($subscription, $advanceCycle, $status);

        return response()->json([
            'message' => 'Invoice generated successfully',
            'data' => $invoice,
        ], 201);
    }

    /**
     * Run automated cycle-end billing for all due subscriptions.
     */
    public function processCycle(Request $request, Merchant $merchant): JsonResponse
    {
        $asOf = $request->input('as_of') ? Carbon::parse($request->input('as_of')) : Carbon::now();

        $invoices = $this->billingService->processDueSubscriptions($merchant->id, $asOf);

        return response()->json([
            'message' => sprintf('Processed billing cycle. Generated %d invoice(s).', $invoices->count()),
            'count' => $invoices->count(),
            'invoices' => $invoices,
        ]);
    }

    /**
     * List all invoices for a merchant.
     */
    public function invoices(Merchant $merchant): JsonResponse
    {
        $invoices = $merchant->invoices()
            ->with(['customer', 'subscription.plan', 'items'])
            ->latest('issued_at')
            ->get();

        return response()->json(['data' => $invoices]);
    }

    /**
     * Retrieve a specific invoice and its line items.
     */
    public function invoiceDetails(Merchant $merchant, Invoice $invoice): JsonResponse
    {
        if ($invoice->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Invoice does not belong to this merchant'], 404);
        }

        return response()->json([
            'data' => $invoice->load(['customer', 'subscription.plan', 'items'])
        ]);
    }
}

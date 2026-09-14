<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Merchant $merchant): JsonResponse
    {
        $customers = $merchant->customers()
            ->with(['activeSubscription.plan'])
            ->withCount('invoices')
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request, Merchant $merchant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'external_id' => 'nullable|string|max:100',
        ]);

        $validated['merchant_id'] = $merchant->id;

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    public function show(Merchant $merchant, Customer $customer): JsonResponse
    {
        if ($customer->merchant_id !== $merchant->id) {
            return response()->json(['error' => 'Customer does not belong to this merchant'], 404);
        }

        return response()->json([
            'data' => $customer->load([
                'subscriptions.plan',
                'invoices.items',
                'dailyUsages' => fn($q) => $q->orderByDesc('usage_date')->limit(30)
            ])
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Plan;
use App\Services\PlanCacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantPlanController extends Controller
{
    public function __construct(
        protected PlanCacheService $planCacheService
    ) {}

    /**
     * Define and store a new Plan for the given Merchant.
     */
    public function store(Request $request, Merchant $merchant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'base_price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,annual,weekly,daily,custom',
            'cycle_days' => 'nullable|integer|min:1|max:365',
            'included_usage_units' => 'required|integer|min:0',
            'overage_rate_per_unit' => 'required|numeric|min:0',
            'prorate_allowance' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Default cycle_days based on billing_cycle if not explicitly set
        if (empty($validated['cycle_days'])) {
            $validated['cycle_days'] = match ($validated['billing_cycle']) {
                'annual' => 365,
                'weekly' => 7,
                'daily' => 1,
                default => 30, // monthly or default
            };
        }

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $baseSlug = Str::slug($validated['name']);
            $code = $baseSlug;
            $counter = 1;
            while (Plan::where('merchant_id', $merchant->id)->where('code', $code)->exists()) {
                $code = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $validated['code'] = $code;
        }

        $validated['merchant_id'] = $merchant->id;
        $validated['prorate_allowance'] = $request->boolean('prorate_allowance', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        $plan = Plan::create($validated);

        return redirect()->route('dashboard', ['merchant_id' => $merchant->id, 'tab' => 'plans'])
            ->with('success', "Plan '{$plan->name}' successfully defined! It is now active and cached for customers.");
    }

    /**
     * Update an existing Plan for the given Merchant.
     */
    public function update(Request $request, Merchant $merchant, Plan $plan): RedirectResponse
    {
        if ($plan->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized plan modification.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'base_price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,annual,weekly,daily,custom',
            'cycle_days' => 'nullable|integer|min:1|max:365',
            'included_usage_units' => 'required|integer|min:0',
            'overage_rate_per_unit' => 'required|numeric|min:0',
            'prorate_allowance' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['cycle_days'])) {
            $validated['cycle_days'] = match ($validated['billing_cycle']) {
                'annual' => 365,
                'weekly' => 7,
                'daily' => 1,
                default => 30,
            };
        }

        $validated['prorate_allowance'] = $request->boolean('prorate_allowance', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $plan->update($validated);

        return redirect()->route('dashboard', ['merchant_id' => $merchant->id, 'tab' => 'plans'])
            ->with('success', "Plan '{$plan->name}' updated successfully! Pricing cache automatically invalidated.");
    }

    /**
     * Toggle a Plan's active status.
     */
    public function toggle(Merchant $merchant, Plan $plan): RedirectResponse
    {
        if ($plan->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized plan modification.');
        }

        $plan->is_active = !$plan->is_active;
        $plan->save();

        $statusStr = $plan->is_active ? 'activated' : 'archived';

        return redirect()->route('dashboard', ['merchant_id' => $merchant->id, 'tab' => 'plans'])
            ->with('success', "Plan '{$plan->name}' has been {$statusStr}.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\DashboardMetricsService;
use App\Services\PlanCacheService;
use App\Services\UsageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MerchantPortalController extends Controller
{
    public function __construct(
        protected UsageService $usageService,
        protected BillingService $billingService,
        protected DashboardMetricsService $metricsService,
        protected PlanCacheService $planCacheService
    ) {}

    /**
     * Show merchant login page with 1-click tenant selector.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('merchant_id')) {
            return redirect()->route('merchant.dashboard');
        }

        $merchants = Merchant::withCount(['plans', 'customers'])->orderBy('id')->get();

        return view('merchant.login', [
            'merchants' => $merchants,
        ]);
    }

    /**
     * Authenticate merchant by email or selected ID.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_id' => 'nullable|integer|exists:merchants,id',
            'email' => 'nullable|email',
            'password' => 'nullable|string',
        ]);

        if (!empty($validated['merchant_id'])) {
            $merchant = Merchant::find($validated['merchant_id']);
        } elseif (!empty($validated['email'])) {
            $merchant = Merchant::where('email', $validated['email'])->first();
        } else {
            return back()->withInput()->withErrors(['email' => 'Please provide a valid email or select a merchant account.']);
        }

        if (!$merchant) {
            return back()->withInput()->withErrors(['email' => 'No merchant found with that email address.']);
        }

        $password = $request->input('password', '123456');
        if (!Hash::check($password, $merchant->password)) {
            return back()->withInput()->withErrors(['password' => 'The provided password is incorrect. (Default is 123456)']);
        }

        $request->session()->put('merchant_id', $merchant->id);

        return redirect()->route('merchant.dashboard')->with('success', "Welcome to the Merchant Portal, {$merchant->name}!");
    }

    /**
     * Log out of the merchant portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('merchant_id');

        return redirect()->route('merchant.login')->with('success', 'You have been successfully logged out.');
    }

    /**
     * Merchant Portal Dashboard.
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $merchantId = $request->session()->get('merchant_id');
        if (!$merchantId) {
            return redirect()->route('merchant.login')->withErrors(['auth' => 'Please log in to access the Merchant Portal.']);
        }

        $merchant = Merchant::with([
            'plans' => fn($q) => $q->withCount('subscriptions')->orderBy('base_price'),
            'customers',
        ])->findOrFail($merchantId);

        $subscriptions = [];
        $subs = Subscription::where('merchant_id', $merchant->id)
            ->with(['customer', 'plan'])
            ->latest()
            ->get();

        $totalUnitsCycle = 0;
        foreach ($subs as $sub) {
            $billingDetails = $this->billingService->calculateBillingDetails($sub);
            $subscriptions[] = [
                'model' => $sub,
                'billing' => $billingDetails,
            ];
            $totalUnitsCycle += $billingDetails['actual_units'];
        }

        $invoices = Invoice::where('merchant_id', $merchant->id)
            ->with(['customer', 'items', 'subscription.plan'])
            ->latest('issued_at')
            ->get();

        $totalBilled = $invoices->sum('total_amount');
        $metrics = $this->metricsService->getMetrics($merchant);

        return view('merchant.dashboard', [
            'merchant' => $merchant,
            'subscriptions' => $subscriptions,
            'invoices' => $invoices,
            'totalUnitsCycle' => $totalUnitsCycle,
            'totalBilled' => $totalBilled,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Define and store a new Plan for the logged-in Merchant.
     */
    public function storePlan(Request $request): RedirectResponse
    {
        $merchantId = $request->session()->get('merchant_id');
        if (!$merchantId) {
            return redirect()->route('merchant.login');
        }

        $merchant = Merchant::findOrFail($merchantId);

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

        if (empty($validated['cycle_days'])) {
            $validated['cycle_days'] = match ($validated['billing_cycle']) {
                'annual' => 365,
                'weekly' => 7,
                'daily' => 1,
                default => 30,
            };
        }

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

        return redirect()->route('merchant.dashboard', ['tab' => 'plans'])
            ->with('success', "Plan '{$plan->name}' successfully defined! It is immediately active, cached, and available in the Customer Portal.");
    }

    /**
     * Toggle a Plan's active status.
     */
    public function togglePlan(Request $request, Plan $plan): RedirectResponse
    {
        $merchantId = $request->session()->get('merchant_id');
        if (!$merchantId || $plan->merchant_id !== (int) $merchantId) {
            abort(403, 'Unauthorized plan modification.');
        }

        $plan->is_active = !$plan->is_active;
        $plan->save();

        $statusStr = $plan->is_active ? 'activated' : 'archived';

        return redirect()->route('merchant.dashboard', ['tab' => 'plans'])
            ->with('success', "Plan '{$plan->name}' has been {$statusStr}.");
    }

    /**
     * Update merchant account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $merchantId = $request->session()->get('merchant_id');
        if (!$merchantId) {
            return redirect()->route('merchant.login');
        }

        $merchant = Merchant::findOrFail($merchantId);

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $merchant->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
        }

        $merchant->password = Hash::make($request->password);
        $merchant->save();

        return back()->with('success', 'Your merchant account password has been successfully updated!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\PlanCacheService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected BillingService $billingService,
        protected PlanCacheService $planCacheService
    ) {}

    /**
     * Show customer portal login page with 1-click demo customer selector.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('customer_id')) {
            return redirect()->route('portal.dashboard');
        }

        $merchants = Merchant::with(['customers.activeSubscription.plan'])->get();

        return view('portal.login', [
            'merchants' => $merchants,
        ]);
    }

    /**
     * Authenticate customer by email or selected ID.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer|exists:customers,id',
            'email' => 'nullable|email',
            'password' => 'nullable|string',
        ]);

        if (!empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
        } elseif (!empty($validated['email'])) {
            $customer = Customer::where('email', $validated['email'])->first();
        } else {
            return back()->withInput()->withErrors(['email' => 'Please provide an email or select an account.']);
        }

        if (!$customer) {
            return back()->withInput()->withErrors(['email' => 'No customer account found with that email address.']);
        }

        $password = $request->input('password', '123456');
        if (!Hash::check($password, $customer->password)) {
            return back()->withInput()->withErrors(['password' => 'The provided password is incorrect. (Default is 123456)']);
        }

        $request->session()->put('customer_id', $customer->id);

        return redirect()->route('portal.dashboard')->with('success', "Welcome back, {$customer->name}!");
    }

    /**
     * Log out of customer portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('customer_id');

        return redirect()->route('portal.login')->with('success', 'You have been successfully logged out.');
    }

    /**
     * Customer Portal Dashboard.
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $customerId = $request->session()->get('customer_id');
        if (!$customerId) {
            return redirect()->route('portal.login')->withErrors(['auth' => 'Please log in to access your customer portal.']);
        }

        $customer = Customer::with(['merchant', 'invoices.items'])->findOrFail($customerId);

        $activeSub = Subscription::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->with(['plan', 'segments.plan'])
            ->latest('starts_at')
            ->first();

        $billingDetails = null;
        if ($activeSub) {
            $billingDetails = $this->billingService->calculateBillingDetails($activeSub);
        }

        // Available plans for the customer's merchant
        $allPlans = $this->planCacheService->getPlansForMerchant($customer->merchant_id);

        return view('portal.dashboard', [
            'customer' => $customer,
            'merchant' => $customer->merchant,
            'activeSubscription' => $activeSub,
            'billing' => $billingDetails,
            'plans' => $allPlans,
            'invoices' => $customer->invoices()->latest('issued_at')->get(),
        ]);
    }

    /**
     * Purchase a new plan if the customer does not have an active subscription.
     */
    public function purchasePlan(Request $request): RedirectResponse
    {
        $customerId = $request->session()->get('customer_id');
        if (!$customerId) {
            return redirect()->route('portal.login');
        }

        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $plan = Plan::where('merchant_id', $customer->merchant_id)->findOrFail($validated['plan_id']);

        $existing = Subscription::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->withErrors(['plan' => 'You already have an active subscription. Use the upgrade or downgrade option instead.']);
        }

        $sub = $this->subscriptionService->subscribe($customer, $plan);

        return redirect()->route('portal.dashboard')->with('success', "Congratulations! You have successfully subscribed to the {$plan->name}.");
    }

    /**
     * Upgrade or Downgrade an active subscription mid-cycle.
     */
    public function changePlan(Request $request): RedirectResponse
    {
        $customerId = $request->session()->get('customer_id');
        if (!$customerId) {
            return redirect()->route('portal.login');
        }

        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'subscription_id' => 'required|integer|exists:subscriptions,id',
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $subscription = Subscription::where('customer_id', $customer->id)->findOrFail($validated['subscription_id']);
        $newPlan = Plan::where('merchant_id', $customer->merchant_id)->findOrFail($validated['plan_id']);

        if ($subscription->plan_id === $newPlan->id) {
            return back()->withErrors(['plan' => 'You are already on this plan.']);
        }

        $oldPlanName = $subscription->plan->name;
        $isUpgrade = (float) $newPlan->base_price > (float) $subscription->plan->base_price;
        $actionType = $isUpgrade ? 'upgraded' : 'downgraded';

        // Execute mid-cycle change immediately
        $this->subscriptionService->changePlan($subscription, $newPlan, Carbon::now());

        return redirect()->route('portal.dashboard')->with('success', "Plan successfully {$actionType} from {$oldPlanName} to {$newPlan->name}! Your billing period has been segmented with prorated allowances and rates.");
    }

    /**
     * Update customer account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $customerId = $request->session()->get('customer_id');
        if (!$customerId) {
            return redirect()->route('portal.login');
        }

        $customer = Customer::findOrFail($customerId);

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
        }

        $customer->password = Hash::make($request->password);
        $customer->save();

        return back()->with('success', 'Your customer account password has been successfully updated!');
    }
}

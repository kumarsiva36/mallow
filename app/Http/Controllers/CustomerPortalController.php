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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerPortalController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected BillingService $billingService,
        protected PlanCacheService $planCacheService
    ) {}

    /**
     * Show customer portal login page with 1-click demo customer selector and sign-up option.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('customer_id')) {
            return redirect()->route('portal.dashboard');
        }

        $merchants = Merchant::with([
            'customers.activeSubscription.plan',
            'plans' => fn($q) => $q->where('is_active', true)->orderBy('base_price'),
        ])->get();

        return view('portal.login', [
            'merchants' => $merchants,
            'initialTab' => $request->query('tab', 'login'),
            'selectedMerchantId' => $request->query('merchant_id', $merchants->first()?->id),
            'selectedPlanId' => $request->query('plan_id'),
        ]);
    }

    /**
     * Show customer registration page with tenant selection and plan options.
     */
    public function showRegister(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('customer_id')) {
            return redirect()->route('portal.dashboard');
        }

        $merchants = Merchant::with([
            'customers.activeSubscription.plan',
            'plans' => fn($q) => $q->where('is_active', true)->orderBy('base_price'),
        ])->get();

        $selectedMerchantId = $request->query('merchant_id', $merchants->first()?->id);
        $selectedPlanId = $request->query('plan_id');

        return view('portal.login', [
            'merchants' => $merchants,
            'initialTab' => 'register',
            'selectedMerchantId' => $selectedMerchantId,
            'selectedPlanId' => $selectedPlanId,
        ]);
    }

    /**
     * Handle customer registration and initial account setup.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'merchant_id' => 'required|integer|exists:merchants,id',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers')->where(fn ($query) => $query->where('merchant_id', $request->input('merchant_id'))),
            ],
            'password' => 'required|string|min:6|confirmed',
            'plan_id' => 'nullable|integer|exists:plans,id',
        ], [
            'email.unique' => 'An account with this email already exists for this provider. Please log in or use another email.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 6 characters.',
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'external_id' => 'cust_' . Str::lower(Str::random(8)),
        ]);

        $subscribedPlan = null;
        if (!empty($validated['plan_id'])) {
            $plan = Plan::where('merchant_id', $merchant->id)
                ->where('is_active', true)
                ->find($validated['plan_id']);

            if ($plan) {
                $this->subscriptionService->subscribe($customer, $plan);
                $subscribedPlan = $plan;
            }
        }

        $request->session()->put('customer_id', $customer->id);

        $welcomeMessage = $subscribedPlan
            ? "Welcome to {$merchant->name}! Your account has been created and your subscription to {$subscribedPlan->name} is active."
            : "Welcome to {$merchant->name}! Your customer account has been created successfully. Choose a plan to start using services.";

        return redirect()->route('portal.dashboard')->with('success', $welcomeMessage);
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

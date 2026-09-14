<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\BillingService;
use App\Services\DashboardMetricsService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected UsageService $usageService,
        protected BillingService $billingService,
        protected DashboardMetricsService $metricsService
    ) {}

    public function index(Request $request): View
    {
        $merchants = Merchant::orderBy('id')->get();
        $selectedMerchantId = $request->query('merchant_id', $merchants->first()?->id);
        $merchant = Merchant::with([
            'plans' => fn($q) => $q->withCount('subscriptions')->orderBy('base_price'),
            'customers',
        ])->find($selectedMerchantId) ?? $merchants->first();

        $subscriptions = [];
        $invoices = [];
        $totalUnitsCycle = 0;
        $totalBilled = 0;

        if ($merchant) {
            $subs = Subscription::where('merchant_id', $merchant->id)
                ->with(['customer', 'plan'])
                ->latest()
                ->get();

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
        } else {
            $metrics = [
                'top_customers' => [],
                'projected_overage_revenue' => [
                    'currency' => 'USD',
                    'accrued_overage_amount' => 0.0,
                    'projected_overage_amount' => 0.0,
                    'active_subscriptions_count' => 0,
                    'subscriptions_with_overage_count' => 0,
                ],
                'churn_risk_customers' => [],
            ];
        }

        return view('dashboard', [
            'merchants' => $merchants,
            'currentMerchant' => $merchant,
            'subscriptions' => $subscriptions,
            'invoices' => $invoices,
            'totalUnitsCycle' => $totalUnitsCycle,
            'totalBilled' => $totalBilled,
            'metrics' => $metrics,
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\DailyUsage;
use App\Models\Merchant;
use App\Models\Subscription;
use Carbon\Carbon;

class DashboardMetricsService
{
    public function __construct(
        protected BillingService $billingService,
        protected UsageService $usageService
    ) {}

    /**
     * Compute full dashboard metrics for a merchant.
     */
    public function getMetrics(Merchant $merchant, ?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now();

        $topCustomers = $this->getTopCustomersThisMonth($merchant, $now);
        $projectedOverage = $this->getProjectedOverageRevenue($merchant, $now);
        $churnRisk = $this->getChurnRiskCustomers($merchant, $now);
        $dailyTrend = $this->getDailyUsageTrend($merchant, $now);
        $cycleSummary = $this->getCurrentCycleSummary($merchant, $now);

        return [
            'merchant_id' => $merchant->id,
            'merchant_name' => $merchant->name,
            'currency' => $merchant->currency,
            'as_of' => $now->toDateTimeString(),
            'current_cycle_usage' => $cycleSummary['current_cycle_usage'],
            'active_plan' => $cycleSummary['active_plan'],
            'top_customers' => $topCustomers,
            'projected_overage_revenue' => $projectedOverage,
            'churn_risk_customers' => $churnRisk,
            'daily_usage_trend' => $dailyTrend,
        ];
    }

    /**
     * Requirement 5a: Top 5 customers by usage this month (with % of Allowance).
     */
    public function getTopCustomersThisMonth(Merchant $merchant, Carbon $now): array
    {
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $endOfMonth = $now->copy()->endOfMonth()->toDateString();

        $topUsage = DailyUsage::where('merchant_id', $merchant->id)
            ->whereBetween('usage_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('customer_id, SUM(total_units) as total_units, SUM(event_count) as total_events')
            ->groupBy('customer_id')
            ->orderByDesc('total_units')
            ->limit(5)
            ->get();

        return $topUsage->map(function ($row) {
            $customer = Customer::with(['subscriptions' => fn($q) => $q->where('status', 'active')->with('plan')])
                ->find($row->customer_id);
            $activeSub = $customer?->subscriptions->first();
            $allowance = (int) ($activeSub?->plan?->included_usage_units ?? 0);
            $units = (int) $row->total_units;
            $pct = $allowance > 0 ? (int) round(($units / $allowance) * 100) : 100;

            return [
                'customer_id' => (int) $row->customer_id,
                'name' => $customer?->name ?? 'Customer #' . $row->customer_id,
                'email' => $customer?->email ?? '',
                'plan_name' => $activeSub?->plan?->name ?? 'None',
                'total_usage_units' => $units,
                'total_events' => (int) $row->total_events,
                'allowance' => $allowance,
                'percentage_of_allowance' => $pct,
            ];
        })->values()->all();
    }

    /**
     * Wireframe KPI: Current cycle usage and active plan summary.
     */
    public function getCurrentCycleSummary(Merchant $merchant, Carbon $now): array
    {
        $activeSubscriptions = Subscription::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->with(['plan', 'customer'])
            ->get();

        $totalUnits = 0;
        $totalAllowance = 0;

        foreach ($activeSubscriptions as $sub) {
            $details = $this->billingService->calculateBillingDetails($sub, $now);
            $totalUnits += $details['actual_units'];
            $totalAllowance += $details['effective_allowance'];
        }

        $primaryPlan = $activeSubscriptions->first()?->plan;
        $planDisplay = $primaryPlan
            ? "{$primaryPlan->name} — {$primaryPlan->billing_cycle}"
            : "Standard — monthly";

        return [
            'current_cycle_usage' => [
                'used_units' => $totalUnits,
                'total_allowance' => max(1, $totalAllowance),
                'formatted' => number_format($totalUnits) . ' / ' . number_format($totalAllowance) . ' units',
                'percentage' => $totalAllowance > 0 ? min(100, round(($totalUnits / $totalAllowance) * 100, 1)) : 0,
            ],
            'active_plan' => $planDisplay,
        ];
    }

    /**
     * Wireframe Chart: Daily usage trend over the last 30 days.
     */
    public function getDailyUsageTrend(Merchant $merchant, Carbon $now): array
    {
        $startDate = $now->copy()->subDays(29)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $dailyRows = DailyUsage::where('merchant_id', $merchant->id)
            ->whereBetween('usage_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('usage_date')
            ->selectRaw('usage_date, SUM(total_units) as units')
            ->pluck('units', 'usage_date');

        $trend = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->toDateString();
            $trend[] = [
                'date' => $dateStr,
                'day' => $current->format('M d'),
                'units' => (int) ($dailyRows[$dateStr] ?? 0),
            ];
            $current->addDay();
        }

        return $trend;
    }

    /**
     * Requirement 5b: Projected overage revenue for the current cycle across active subscriptions.
     */
    public function getProjectedOverageRevenue(Merchant $merchant, Carbon $now): array
    {
        $activeSubscriptions = Subscription::where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->with(['plan', 'customer'])
            ->get();

        $accruedOverage = 0.0;
        $projectedOverage = 0.0;
        $subscriptionsWithOverage = 0;

        foreach ($activeSubscriptions as $sub) {
            $billingDetails = $this->billingService->calculateBillingDetails($sub, $now);
            $accruedOverage += $billingDetails['overage_amount'];

            if ($billingDetails['overage_units'] > 0) {
                $subscriptionsWithOverage++;
            }

            // Run-rate projection to cycle end
            $cycleStart = $sub->current_cycle_start;
            $cycleEnd = $sub->current_cycle_end;
            $totalCycleSeconds = max(1, $cycleStart->diffInSeconds($cycleEnd));
            $elapsedSeconds = max(1, min($totalCycleSeconds, $cycleStart->diffInSeconds($now)));

            $effectiveAllowance = $billingDetails['effective_allowance'];
            $actualUnits = $billingDetails['actual_units'];
            $overageRate = (float) $sub->plan->overage_rate_per_unit;

            $projectedUnits = (int) round(($actualUnits / $elapsedSeconds) * $totalCycleSeconds);
            $projectedOverageUnits = max(0, $projectedUnits - $effectiveAllowance);
            $projectedSubOverage = round($projectedOverageUnits * $overageRate, 2);

            $projectedOverage += max($billingDetails['overage_amount'], $projectedSubOverage);
        }

        return [
            'currency' => $merchant->currency,
            'accrued_overage_amount' => round($accruedOverage, 2),
            'projected_overage_amount' => round($projectedOverage, 2),
            'active_subscriptions_count' => $activeSubscriptions->count(),
            'subscriptions_with_overage_count' => $subscriptionsWithOverage,
        ];
    }

    /**
     * Requirement 5c: Customers whose usage dropped >50% month-over-month (churn risk).
     */
    public function getChurnRiskCustomers(Merchant $merchant, Carbon $now): array
    {
        $currentMonthStart = $now->copy()->startOfMonth()->toDateString();
        $currentMonthEnd = $now->copy()->endOfMonth()->toDateString();

        $prevMonthStart = $now->copy()->subMonth()->startOfMonth()->toDateString();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth()->toDateString();

        $prevUsage = DailyUsage::where('merchant_id', $merchant->id)
            ->whereBetween('usage_date', [$prevMonthStart, $prevMonthEnd])
            ->groupBy('customer_id')
            ->selectRaw('customer_id, SUM(total_units) as units')
            ->pluck('units', 'customer_id');

        $currentUsage = DailyUsage::where('merchant_id', $merchant->id)
            ->whereBetween('usage_date', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('customer_id')
            ->selectRaw('customer_id, SUM(total_units) as units')
            ->pluck('units', 'customer_id');

        $churnRisk = [];

        foreach ($prevUsage as $customerId => $prevUnits) {
            $prevUnits = (int) $prevUnits;
            if ($prevUnits <= 0) {
                continue;
            }

            $currUnits = (int) ($currentUsage[$customerId] ?? 0);
            $dropRatio = ($prevUnits - $currUnits) / $prevUnits;

            if ($dropRatio > 0.50) {
                $customer = Customer::find($customerId);
                $churnRisk[] = [
                    'customer_id' => (int) $customerId,
                    'name' => $customer?->name ?? 'Customer #' . $customerId,
                    'email' => $customer?->email ?? '',
                    'previous_month_usage' => $prevUnits,
                    'current_month_usage' => $currUnits,
                    'drop_percentage' => round($dropRatio * 100, 1),
                    'risk_level' => $dropRatio >= 0.80 ? 'critical' : 'high',
                ];
            }
        }

        // Sort by drop percentage descending
        usort($churnRisk, fn($a, $b) => $b['drop_percentage'] <=> $a['drop_percentage']);

        return $churnRisk;
    }
}

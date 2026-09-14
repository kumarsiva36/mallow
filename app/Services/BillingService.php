<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    public function __construct(
        protected UsageService $usageService,
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Calculate the proration factor between 0.0000 and 1.0000.
     */
    public function calculateProrationRatio(
        Carbon $startsAt,
        Carbon $cycleStart,
        Carbon $cycleEnd,
        ?int $cycleDays = null
    ): float {
        // If started at or before the cycle start, full cycle applies
        if ($startsAt->lte($cycleStart)) {
            return 1.0;
        }

        // If started after cycle ended
        if ($startsAt->gte($cycleEnd)) {
            return 0.0;
        }

        $totalCycleDays = $cycleDays ?: max(1, (int) round($cycleStart->diffInDays($cycleEnd)));
        
        // Active days between startsAt and cycleEnd
        $activeDays = max(1, (int) ceil($startsAt->diffInSeconds($cycleEnd) / 86400));
        $activeDays = min($activeDays, $totalCycleDays);

        return round($activeDays / $totalCycleDays, 4);
    }

    /**
     * Compute invoice details without persisting (dry run / preview).
     */
    public function calculateBillingDetails(Subscription $subscription, ?Carbon $asOf = null): array
    {
        $plan = $subscription->plan;
        $merchant = $subscription->merchant;
        $customer = $subscription->customer;

        $cycleStart = $subscription->current_cycle_start;
        $cycleEnd = $subscription->current_cycle_end;
        $startsAt = $subscription->starts_at;

        // Check if subscription has multiple plan segments for this billing cycle
        $segments = $subscription->segments()
            ->where('starts_at', '<', $cycleEnd)
            ->where('ends_at', '>', $cycleStart)
            ->with('plan')
            ->orderBy('starts_at', 'asc')
            ->get();

        if ($segments->count() > 1) {
            $totalCycleDays = $plan->cycle_days ?: max(1, (int) round($cycleStart->diffInDays($cycleEnd)));
            $segmentDetails = [];
            $totalBaseAmount = 0.0;
            $totalOverageAmount = 0.0;
            $totalActualUnits = 0;
            $totalEffectiveAllowance = 0;
            $totalOverageUnits = 0;

            foreach ($segments as $seg) {
                $segPlan = $seg->plan;
                $segStart = $seg->starts_at->gt($cycleStart) ? $seg->starts_at : $cycleStart;
                $segEnd = $seg->ends_at->lt($cycleEnd) ? $seg->ends_at : $cycleEnd;

                $segSeconds = max(0, $segStart->diffInSeconds($segEnd));
                $segDays = max(1, (int) round($segSeconds / 86400));
                $segDays = min($segDays, $totalCycleDays);

                $segRatio = round($segDays / $totalCycleDays, 4);
                $segBase = round(((float)$segPlan->base_price) * $segRatio, 2);

                $segAllowance = $segPlan->prorate_allowance
                    ? (int) round($segPlan->included_usage_units * $segRatio)
                    : (int) $segPlan->included_usage_units;

                $segUnits = $this->usageService->getUsageForPeriod(
                    $merchant->id,
                    $customer->id,
                    $segStart,
                    $segEnd,
                    'api_calls'
                );

                $segOverageUnits = max(0, $segUnits - $segAllowance);
                $segOverageRate = (float) $segPlan->overage_rate_per_unit;
                $segOverage = round($segOverageUnits * $segOverageRate, 2);

                $totalBaseAmount += $segBase;
                $totalOverageAmount += $segOverage;
                $totalActualUnits += $segUnits;
                $totalEffectiveAllowance += $segAllowance;
                $totalOverageUnits += $segOverageUnits;

                $segmentDetails[] = [
                    'segment_id' => $seg->id,
                    'plan_id' => $segPlan->id,
                    'plan_name' => $segPlan->name,
                    'period_start' => $segStart,
                    'period_end' => $segEnd,
                    'active_days' => $segDays,
                    'cycle_days' => $totalCycleDays,
                    'proration_ratio' => $segRatio,
                    'base_price' => (float) $segPlan->base_price,
                    'base_amount' => $segBase,
                    'included_units' => (int) $segPlan->included_usage_units,
                    'effective_allowance' => $segAllowance,
                    'actual_units' => $segUnits,
                    'overage_units' => $segOverageUnits,
                    'overage_rate' => $segOverageRate,
                    'overage_amount' => $segOverage,
                    'total_amount' => round($segBase + $segOverage, 2),
                ];
            }

            $totalAmount = round($totalBaseAmount + $totalOverageAmount, 2);

            return [
                'subscription_id' => $subscription->id,
                'merchant_id' => $merchant->id,
                'customer_id' => $customer->id,
                'plan_name' => $plan->name,
                'period_start' => $cycleStart,
                'period_end' => $cycleEnd,
                'cycle_start' => $cycleStart,
                'cycle_end' => $cycleEnd,
                'starts_at' => $startsAt,
                'is_prorated' => true,
                'proration_ratio' => 1.0,
                'plan_base_price' => (float)$plan->base_price,
                'base_amount' => round($totalBaseAmount, 2),
                'plan_included_units' => (int)$plan->included_usage_units,
                'effective_allowance' => $totalEffectiveAllowance,
                'actual_units' => $totalActualUnits,
                'overage_units' => $totalOverageUnits,
                'overage_rate' => (float)$plan->overage_rate_per_unit,
                'overage_amount' => round($totalOverageAmount, 2),
                'total_amount' => $totalAmount,
                'currency' => $merchant->currency,
                'is_segmented' => true,
                'segments' => $segmentDetails,
            ];
        }

        // Effective start for usage and billing
        $effectiveStart = $startsAt->gt($cycleStart) ? $startsAt : $cycleStart;

        // Calculate proration
        $prorationRatio = $this->calculateProrationRatio($startsAt, $cycleStart, $cycleEnd, $plan->cycle_days);

        // Prorated base price
        $baseAmount = round(((float)$plan->base_price) * $prorationRatio, 2);

        // Calculate effective included units
        $effectiveAllowance = $plan->prorate_allowance
            ? (int) round($plan->included_usage_units * $prorationRatio)
            : (int) $plan->included_usage_units;

        // Metered usage
        $actualUnits = $this->usageService->getUsageForPeriod(
            $merchant->id,
            $customer->id,
            $effectiveStart,
            $cycleEnd,
            'api_calls'
        );

        // Overage calculation
        $overageUnits = max(0, $actualUnits - $effectiveAllowance);
        $overageAmount = round($overageUnits * ((float)$plan->overage_rate_per_unit), 2);
        $totalAmount = round($baseAmount + $overageAmount, 2);

        return [
            'subscription_id' => $subscription->id,
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_name' => $plan->name,
            'period_start' => $effectiveStart,
            'period_end' => $cycleEnd,
            'cycle_start' => $cycleStart,
            'cycle_end' => $cycleEnd,
            'starts_at' => $startsAt,
            'is_prorated' => $prorationRatio < 1.0,
            'proration_ratio' => $prorationRatio,
            'plan_base_price' => (float)$plan->base_price,
            'base_amount' => $baseAmount,
            'plan_included_units' => (int)$plan->included_usage_units,
            'effective_allowance' => $effectiveAllowance,
            'actual_units' => $actualUnits,
            'overage_units' => $overageUnits,
            'overage_rate' => (float)$plan->overage_rate_per_unit,
            'overage_amount' => $overageAmount,
            'total_amount' => $totalAmount,
            'currency' => $merchant->currency,
            'is_segmented' => false,
            'segments' => [],
        ];
    }

    /**
     * Generate and persist an invoice for the subscription's current cycle.
     */
    public function generateInvoice(
        Subscription $subscription,
        bool $advanceCycle = true,
        string $status = 'issued'
    ): Invoice {
        $details = $this->calculateBillingDetails($subscription);

        return DB::transaction(function () use ($subscription, $details, $advanceCycle, $status) {
            $invoiceNumber = sprintf(
                'INV-%d-%s-%s',
                $details['merchant_id'],
                Carbon::now()->format('Ymd'),
                strtoupper(Str::random(6))
            );

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'merchant_id' => $details['merchant_id'],
                'customer_id' => $details['customer_id'],
                'subscription_id' => $subscription->id,
                'period_start' => $details['period_start'],
                'period_end' => $details['period_end'],
                'base_amount' => $details['base_amount'],
                'overage_amount' => $details['overage_amount'],
                'total_amount' => $details['total_amount'],
                'proration_ratio' => $details['proration_ratio'],
                'status' => $status,
                'currency' => $details['currency'],
                'issued_at' => Carbon::now(),
            ]);

            if (!empty($details['is_segmented']) && !empty($details['segments'])) {
                foreach ($details['segments'] as $seg) {
                    // Base item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'type' => 'base_fee',
                        'description' => sprintf(
                            '%s Base Subscription (%s - %s, prorated %d/%d days)',
                            $seg['plan_name'],
                            $seg['period_start']->format('M d'),
                            $seg['period_end']->format('M d'),
                            $seg['active_days'],
                            $seg['cycle_days']
                        ),
                        'quantity' => 1,
                        'unit_price' => $seg['base_amount'],
                        'amount' => $seg['base_amount'],
                    ]);

                    // Allowance item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'type' => 'allowance',
                        'description' => sprintf(
                            '[%s] Included allowance: %s units. Recorded usage: %s units.',
                            $seg['plan_name'],
                            number_format($seg['effective_allowance']),
                            number_format($seg['actual_units'])
                        ),
                        'quantity' => $seg['effective_allowance'],
                        'unit_price' => 0.0000,
                        'amount' => 0.00,
                    ]);

                    // Overage item
                    if ($seg['overage_units'] > 0) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'type' => 'overage',
                            'description' => sprintf(
                                '[%s] Usage Overage: %s units @ %s %.4f / unit',
                                $seg['plan_name'],
                                number_format($seg['overage_units']),
                                $details['currency'],
                                $seg['overage_rate']
                            ),
                            'quantity' => $seg['overage_units'],
                            'unit_price' => $seg['overage_rate'],
                            'amount' => $seg['overage_amount'],
                        ]);
                    }
                }
            } else {
                // Item 1: Base Subscription Fee
                $prorationNote = $details['is_prorated']
                    ? sprintf(' (%.1f%% prorated mid-cycle)', $details['proration_ratio'] * 100)
                    : ' (Full billing cycle)';

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => 'base_fee',
                    'description' => "{$details['plan_name']} Base Subscription{$prorationNote}",
                    'quantity' => 1,
                    'unit_price' => $details['base_amount'],
                    'amount' => $details['base_amount'],
                ]);

                // Item 2: Included Allowance (informational)
                $allowanceNote = $details['is_prorated']
                    ? sprintf('Included allowance: %s units (prorated from %s). Recorded usage: %s units.',
                        number_format($details['effective_allowance']),
                        number_format($details['plan_included_units']),
                        number_format($details['actual_units']))
                    : sprintf('Included allowance: %s units. Recorded usage: %s units.',
                        number_format($details['effective_allowance']),
                        number_format($details['actual_units']));

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => 'allowance',
                    'description' => $allowanceNote,
                    'quantity' => $details['effective_allowance'],
                    'unit_price' => 0.0000,
                    'amount' => 0.00,
                ]);

                // Item 3: Overage (if any)
                if ($details['overage_units'] > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'type' => 'overage',
                        'description' => sprintf(
                            'Usage Overage: %s units @ %s %.4f / unit',
                            number_format($details['overage_units']),
                            $details['currency'],
                            $details['overage_rate']
                        ),
                        'quantity' => $details['overage_units'],
                        'unit_price' => $details['overage_rate'],
                        'amount' => $details['overage_amount'],
                    ]);
                }
            }

            // Advance subscription to next cycle if requested
            if ($advanceCycle) {
                $this->subscriptionService->advanceCycle($subscription);
            }

            return $invoice->load('items', 'merchant', 'customer', 'subscription.plan');
        });
    }

    /**
     * Process cycle-end billing for all subscriptions due for billing.
     */
    public function processDueSubscriptions(?int $merchantId = null, ?Carbon $asOf = null): Collection
    {
        $asOf = $asOf ?? Carbon::now();

        $query = Subscription::query()
            ->where('status', 'active')
            ->where('current_cycle_end', '<=', $asOf);

        if ($merchantId !== null) {
            $query->where('merchant_id', $merchantId);
        }

        $dueSubscriptions = $query->with('plan', 'merchant', 'customer')->get();
        $generatedInvoices = collect();

        foreach ($dueSubscriptions as $subscription) {
            $invoice = $this->generateInvoice($subscription, advanceCycle: true);
            $generatedInvoices->push($invoice);
        }

        return $generatedInvoices;
    }
}

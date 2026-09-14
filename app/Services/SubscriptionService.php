<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionSegment;
use Carbon\Carbon;
use InvalidArgumentException;

class SubscriptionService
{
    /**
     * Subscribe a customer to a plan.
     * Can optionally specify a custom start date or cycle start for mid-cycle simulations.
     */
    public function subscribe(
        Customer $customer,
        Plan $plan,
        ?Carbon $startsAt = null,
        ?Carbon $cycleStart = null
    ): Subscription {
        if ($customer->merchant_id !== $plan->merchant_id) {
            throw new InvalidArgumentException('Customer and Plan must belong to the same merchant.');
        }

        $now = Carbon::now();
        $startsAt = $startsAt ?? $now;

        // If cycleStart is not explicitly provided, default to startsAt or first of month / cycleDays
        $currentCycleStart = $cycleStart ?? $startsAt->copy()->startOfDay();
        $currentCycleEnd = $currentCycleStart->copy()->addDays($plan->cycle_days)->endOfDay();

        $subscription = Subscription::create([
            'merchant_id' => $customer->merchant_id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'current_cycle_start' => $currentCycleStart,
            'current_cycle_end' => $currentCycleEnd,
        ]);

        SubscriptionSegment::create([
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => $currentCycleEnd,
            'billing_cycle_start' => $currentCycleStart,
            'billing_cycle_end' => $currentCycleEnd,
        ]);

        return $subscription;
    }

    /**
     * Upgrade or downgrade a customer's plan mid-cycle.
     * Splits billing into distinct plan segments with prorated allowances and rates.
     */
    public function changePlan(
        Subscription $subscription,
        Plan $newPlan,
        ?Carbon $effectiveAt = null
    ): Subscription {
        if ($newPlan->merchant_id !== $subscription->merchant_id) {
            throw new InvalidArgumentException('New plan must belong to the same merchant.');
        }

        $effectiveAt = $effectiveAt ?? Carbon::now();

        // Find active segment covering this cycle
        $currentSegment = $subscription->segments()
            ->where('billing_cycle_start', $subscription->current_cycle_start)
            ->where('starts_at', '<=', $effectiveAt)
            ->where('ends_at', '>=', $effectiveAt)
            ->latest('id')
            ->first();

        if ($currentSegment) {
            $currentSegment->update(['ends_at' => $effectiveAt]);
        }

        // Create new segment starting from effectiveAt until cycle end
        SubscriptionSegment::create([
            'subscription_id' => $subscription->id,
            'plan_id' => $newPlan->id,
            'starts_at' => $effectiveAt,
            'ends_at' => $subscription->current_cycle_end,
            'billing_cycle_start' => $subscription->current_cycle_start,
            'billing_cycle_end' => $subscription->current_cycle_end,
        ]);

        $subscription->update([
            'plan_id' => $newPlan->id,
        ]);

        return $subscription->fresh(['plan', 'segments']);
    }

    /**
     * Advance a subscription to its next billing cycle.
     */
    public function advanceCycle(Subscription $subscription): Subscription
    {
        $plan = $subscription->plan;
        $nextCycleStart = $subscription->current_cycle_end->copy()->addSecond()->startOfDay();
        $nextCycleEnd = $nextCycleStart->copy()->addDays($plan->cycle_days)->endOfDay();

        $subscription->update([
            'current_cycle_start' => $nextCycleStart,
            'current_cycle_end' => $nextCycleEnd,
        ]);

        // Create new segment for the next billing cycle
        SubscriptionSegment::create([
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => $nextCycleStart,
            'ends_at' => $nextCycleEnd,
            'billing_cycle_start' => $nextCycleStart,
            'billing_cycle_end' => $nextCycleEnd,
        ]);

        return $subscription->fresh(['plan', 'segments']);
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Subscription $subscription, bool $immediately = false): Subscription
    {
        $now = Carbon::now();
        $subscription->update([
            'status' => $immediately ? 'cancelled' : 'pending_cancellation',
            'cancelled_at' => $now,
        ]);

        return $subscription->fresh();
    }
}

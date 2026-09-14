<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PlanCacheService
{
    public const CACHE_TTL_SECONDS = 3600; // 1 hour

    /**
     * Get all active plans for a merchant, utilizing cache.
     */
    public function getPlansForMerchant(int $merchantId): Collection
    {
        $cacheKey = "plans:merchant:{$merchantId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($merchantId) {
            return Plan::where('merchant_id', $merchantId)
                ->withCount('subscriptions')
                ->get();
        });
    }

    /**
     * Get a single plan by ID, utilizing cache.
     */
    public function getPlanById(int $planId): ?Plan
    {
        $cacheKey = "plans:id:{$planId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($planId) {
            return Plan::with('merchant')->find($planId);
        });
    }

    /**
     * Invalidate cached plan lookups when a plan is created, modified, or deleted.
     */
    public function invalidate(Plan $plan): void
    {
        Cache::forget("plans:merchant:{$plan->merchant_id}");
        Cache::forget("plans:id:{$plan->id}");
    }
}

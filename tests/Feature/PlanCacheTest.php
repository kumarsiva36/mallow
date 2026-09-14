<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Plan;
use App\Services\PlanCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PlanCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_lookups_are_cached_and_invalidated_on_change(): void
    {
        Cache::flush();

        $merchant = Merchant::create([
            'name' => 'Cache Test Cloud',
            'slug' => 'cache-cloud',
            'currency' => 'USD',
        ]);

        $plan = Plan::create([
            'merchant_id' => $merchant->id,
            'name' => 'Original Tier',
            'code' => 'original',
            'base_price' => 50.00,
            'cycle_days' => 30,
            'included_usage_units' => 5000,
            'overage_rate_per_unit' => 0.03,
        ]);

        /** @var PlanCacheService $cacheService */
        $cacheService = app(PlanCacheService::class);

        // 1. Initial lookup populates cache
        $this->assertFalse(Cache::has("plans:merchant:{$merchant->id}"));
        $this->assertFalse(Cache::has("plans:id:{$plan->id}"));

        $cachedPlans = $cacheService->getPlansForMerchant($merchant->id);
        $cachedPlan = $cacheService->getPlanById($plan->id);

        $this->assertCount(1, $cachedPlans);
        $this->assertEquals('Original Tier', $cachedPlan->name);

        // Verify keys exist in cache
        $this->assertTrue(Cache::has("plans:merchant:{$merchant->id}"));
        $this->assertTrue(Cache::has("plans:id:{$plan->id}"));

        // 2. Updating plan automatically invalidates the cache
        $plan->update(['name' => 'Updated Tier', 'base_price' => 65.00]);

        $this->assertFalse(Cache::has("plans:merchant:{$merchant->id}"));
        $this->assertFalse(Cache::has("plans:id:{$plan->id}"));

        // Next lookup re-caches updated values
        $freshPlan = $cacheService->getPlanById($plan->id);
        $this->assertEquals('Updated Tier', $freshPlan->name);
        $this->assertEquals(65.00, $freshPlan->base_price);
        $this->assertTrue(Cache::has("plans:id:{$plan->id}"));

        // 3. Deleting plan invalidates cache
        $plan->delete();
        $this->assertFalse(Cache::has("plans:id:{$plan->id}"));
    }
}

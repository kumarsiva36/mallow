<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Plan;
use App\Services\BillingService;
use App\Services\SubscriptionService;
use App\Services\UsageService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(
        UsageService $usageService,
        SubscriptionService $subscriptionService,
        BillingService $billingService
    ): void {
        $now = Carbon::now();

        // 1. Create Merchant 1: Nexus Cloud
        $merchantNexus = Merchant::create([
            'name' => 'Nexus Cloud Platform',
            'slug' => 'nexus-cloud',
            'email' => 'billing@nexuscloud.io',
            'currency' => 'USD',
            'timezone' => 'UTC',
        ]);

        // Plans for Nexus Cloud
        $starterPlan = Plan::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Starter Tier',
            'code' => 'starter-tier',
            'description' => 'Great for prototypes and microservices with up to 1,000 API calls included.',
            'base_price' => 29.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 1000,
            'overage_rate_per_unit' => 0.0500,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $growthPlan = Plan::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Growth Tier',
            'code' => 'growth-tier',
            'description' => 'For fast growing apps. Includes 10,000 API calls with low $0.02 overage.',
            'base_price' => 99.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 10000,
            'overage_rate_per_unit' => 0.0200,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $enterprisePlan = Plan::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Enterprise Scale',
            'code' => 'enterprise-scale',
            'description' => 'Dedicated infrastructure with 100,000 calls included and volume pricing.',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 100000,
            'overage_rate_per_unit' => 0.0100,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        // Customers for Nexus
        $custAcme = Customer::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Acme Corporation',
            'email' => 'devops@acme.corp',
            'external_id' => 'cust_acme_001',
        ]);

        $custGlobex = Customer::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Globex Logistics',
            'email' => 'api@globex.io',
            'external_id' => 'cust_globex_002',
        ]);

        $custInitech = Customer::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Initech Systems',
            'email' => 'admin@initech.net',
            'external_id' => 'cust_initech_003',
        ]);

        $custApex = Customer::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Apex Cloud Systems',
            'email' => 'ops@apexcloud.io',
            'external_id' => 'cust_apex_004',
        ]);

        $custStale = Customer::create([
            'merchant_id' => $merchantNexus->id,
            'name' => 'Stale Innovations',
            'email' => 'accounts@staleinno.com',
            'external_id' => 'cust_stale_005',
        ]);

        // Subscriptions setup
        // Case A: Acme Corp starts MID-CYCLE (e.g. 15 days into a 30-day cycle that started 30 days ago)
        $cycleStart = $now->copy()->subDays(30)->startOfDay();
        $cycleEnd = $now->copy()->subHours(1); // Due for billing!
        $acmeStart = $cycleStart->copy()->addDays(15); // Started mid-cycle (15 active days out of 30)

        $subAcme = $subscriptionService->subscribe(
            $custAcme,
            $growthPlan,
            startsAt: $acmeStart,
            cycleStart: $cycleStart
        );
        $subAcme->update(['current_cycle_end' => $cycleEnd]);
        $subAcme->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Case B: Globex Logistics started at FULL CYCLE (startsAt == cycleStart)
        $subGlobex = $subscriptionService->subscribe(
            $custGlobex,
            $starterPlan,
            startsAt: $cycleStart,
            cycleStart: $cycleStart
        );
        $subGlobex->update(['current_cycle_end' => $cycleEnd]);
        $subGlobex->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Case C: Initech started 5 days ago on a fresh cycle
        $subInitech = $subscriptionService->subscribe(
            $custInitech,
            $enterprisePlan,
            startsAt: $now->copy()->subDays(5),
            cycleStart: $now->copy()->subDays(5)
        );

        // Case D: Apex Cloud Systems - MID-CYCLE PLAN UPGRADE!
        // Subscribed on cycleStart (30 days ago) on Starter Tier.
        // On Day 12, upgraded to Growth Tier!
        $subApex = $subscriptionService->subscribe(
            $custApex,
            $starterPlan,
            startsAt: $cycleStart,
            cycleStart: $cycleStart
        );
        $subApex->update(['current_cycle_end' => $cycleEnd]);
        $subApex->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        $upgradeDate = $cycleStart->copy()->addDays(12);
        $subscriptionService->changePlan($subApex, $growthPlan, $upgradeDate);
        $subApex->segments()->where('plan_id', $growthPlan->id)->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Case E: Stale Innovations - Churn risk customer
        $subStale = $subscriptionService->subscribe(
            $custStale,
            $starterPlan,
            startsAt: $cycleStart->copy()->subDays(30),
            cycleStart: $cycleStart
        );
        $subStale->update(['current_cycle_end' => $cycleEnd]);
        $subStale->segments()->update(['ends_at' => $cycleEnd, 'billing_cycle_end' => $cycleEnd]);

        // Seed Usage Events for Acme (Growth plan: 10,000 units normal, prorated 50% = 5,000 units allowance)
        // We simulate 14,000 units recorded across the 15 active days! (9,000 units overage)
        $acmeEvents = [];
        for ($day = 14; $day >= 0; $day--) {
            $eventDate = $now->copy()->subDays($day);
            $dailyCount = 10;
            $unitsPerEvent = (int) round(14000 / (15 * $dailyCount)); // ~93 units each
            for ($e = 0; $e < $dailyCount; $e++) {
                $acmeEvents[] = [
                    'customer_id' => $custAcme->id,
                    'metric' => 'api_calls',
                    'units' => $unitsPerEvent + rand(-10, 10),
                    'idempotency_key' => 'seed_acme_' . Str::random(12),
                    'recorded_at' => $eventDate->copy()->addMinutes($e * 120)->toDateTimeString(),
                ];
            }
        }
        $usageService->recordBatch($merchantNexus->id, $acmeEvents);

        // Seed Usage Events for Globex (Starter plan: 1,000 units included, full cycle)
        // We simulate 3,200 units recorded (2,200 units overage)
        $globexEvents = [];
        for ($day = 28; $day >= 0; $day -= 2) {
            $eventDate = $now->copy()->subDays($day);
            $globexEvents[] = [
                'customer_id' => $custGlobex->id,
                'metric' => 'api_calls',
                'units' => rand(180, 260),
                'idempotency_key' => 'seed_globex_' . Str::random(12),
                'recorded_at' => $eventDate->toDateTimeString(),
            ];
        }
        $usageService->recordBatch($merchantNexus->id, $globexEvents);

        // Seed Usage Events for Initech (Enterprise plan: 100,000 units, low usage: 4,500 units)
        $initechEvents = [];
        for ($day = 4; $day >= 0; $day--) {
            $eventDate = $now->copy()->subDays($day);
            $initechEvents[] = [
                'customer_id' => $custInitech->id,
                'metric' => 'api_calls',
                'units' => 900,
                'idempotency_key' => 'seed_initech_' . Str::random(12),
                'recorded_at' => $eventDate->toDateTimeString(),
            ];
        }
        $usageService->recordBatch($merchantNexus->id, $initechEvents);

        // Seed Usage Events for Apex Cloud Systems (Mid-Cycle Upgrade):
        // Days 30 to 18 ago (Starter Tier segment): 600 units
        // Days 17 to 0 ago (Growth Tier segment): 12,500 units
        $apexEvents = [];
        // Segment 1 (Starter): Days 28 to 19 ago
        for ($day = 28; $day >= 19; $day -= 2) {
            $apexEvents[] = [
                'customer_id' => $custApex->id,
                'metric' => 'api_calls',
                'units' => 120,
                'idempotency_key' => 'seed_apex_seg1_' . Str::random(12),
                'recorded_at' => $now->copy()->subDays($day)->toDateTimeString(),
            ];
        }
        // Segment 2 (Growth): Days 17 to 1 ago
        for ($day = 17; $day >= 1; $day -= 2) {
            $apexEvents[] = [
                'customer_id' => $custApex->id,
                'metric' => 'api_calls',
                'units' => 1400,
                'idempotency_key' => 'seed_apex_seg2_' . Str::random(12),
                'recorded_at' => $now->copy()->subDays($day)->toDateTimeString(),
            ];
        }
        $usageService->recordBatch($merchantNexus->id, $apexEvents);

        // Seed Usage Events for Stale Innovations (Churn Risk Demo):
        // Previous month (days 55 to 32 ago): 14,000 units
        // Current month (days 25 to 0 ago): only 1,800 units (87% drop!)
        $staleEvents = [];
        for ($day = 55; $day >= 32; $day -= 2) {
            $staleEvents[] = [
                'customer_id' => $custStale->id,
                'metric' => 'api_calls',
                'units' => 1150,
                'idempotency_key' => 'seed_stale_prev_' . Str::random(12),
                'recorded_at' => $now->copy()->subDays($day)->toDateTimeString(),
            ];
        }
        for ($day = 25; $day >= 2; $day -= 6) {
            $staleEvents[] = [
                'customer_id' => $custStale->id,
                'metric' => 'api_calls',
                'units' => 450,
                'idempotency_key' => 'seed_stale_curr_' . Str::random(12),
                'recorded_at' => $now->copy()->subDays($day)->toDateTimeString(),
            ];
        }
        $usageService->recordBatch($merchantNexus->id, $staleEvents);

        // Generate one historical finalized invoice for Globex (previous month) so invoice history has data!
        $prevCycleStart = $cycleStart->copy()->subDays(30);
        $prevCycleEnd = $cycleStart->copy()->subSecond();
        // Record 1 historical invoice
        $histInvoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-1-' . $prevCycleStart->format('Ym') . '-HIST01',
            'merchant_id' => $merchantNexus->id,
            'customer_id' => $custGlobex->id,
            'subscription_id' => $subGlobex->id,
            'period_start' => $prevCycleStart,
            'period_end' => $prevCycleEnd,
            'base_amount' => 29.00,
            'overage_amount' => 45.50,
            'total_amount' => 74.50,
            'proration_ratio' => 1.0000,
            'status' => 'paid',
            'currency' => 'USD',
            'issued_at' => $prevCycleEnd->copy()->addMinutes(5),
        ]);
        \App\Models\InvoiceItem::create([
            'invoice_id' => $histInvoice->id,
            'type' => 'base_fee',
            'description' => 'Starter Tier Base Subscription (Full billing cycle)',
            'quantity' => 1,
            'unit_price' => 29.00,
            'amount' => 29.00,
        ]);
        \App\Models\InvoiceItem::create([
            'invoice_id' => $histInvoice->id,
            'type' => 'allowance',
            'description' => 'Included allowance: 1,000 units. Recorded usage: 1,910 units.',
            'quantity' => 1000,
            'unit_price' => 0.00,
            'amount' => 0.00,
        ]);
        \App\Models\InvoiceItem::create([
            'invoice_id' => $histInvoice->id,
            'type' => 'overage',
            'description' => 'Usage Overage: 910 units @ USD 0.0500 / unit',
            'quantity' => 910,
            'unit_price' => 0.0500,
            'amount' => 45.50,
        ]);

        // 2. Create Merchant 2: Apex AI Solutions (EUR)
        $merchantApex = Merchant::create([
            'name' => 'Apex AI Solutions',
            'slug' => 'apex-ai',
            'email' => 'finance@apex-ai.de',
            'currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
        ]);

        $aiPlan = Plan::create([
            'merchant_id' => $merchantApex->id,
            'name' => 'Model API Professional',
            'code' => 'model-api-pro',
            'description' => 'Inference tokens metering plan. Includes 50,000 tokens.',
            'base_price' => 75.00,
            'billing_cycle' => 'monthly',
            'cycle_days' => 30,
            'included_usage_units' => 50000,
            'overage_rate_per_unit' => 0.0025,
            'prorate_allowance' => true,
            'is_active' => true,
        ]);

        $custSoylent = Customer::create([
            'merchant_id' => $merchantApex->id,
            'name' => 'Soylent Technologies GmbH',
            'email' => 'billing@soylent-tech.de',
            'external_id' => 'cust_soylent_de',
        ]);

        $subscriptionService->subscribe($custSoylent, $aiPlan, startsAt: $now->copy()->subDays(10));
    }
}

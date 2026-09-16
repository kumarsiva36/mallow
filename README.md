# Multi-Tenant SaaS Metering & Cycle-End Billing Engine

> **Senior Laravel Developer Mini Task — Mallow Technologies**  
> An enterprise-grade, high-throughput subscription billing and usage-metering system designed to handle 50L+ raw events, mid-cycle plan upgrades/downgrades with segment proration, atomic rollups, queued chunked billing, caching, rate limiting, and real-time merchant analytics.

---

## 📑 Table of Contents
1. [Architecture & System Overview](#1-architecture--system-overview)
2. [Normalized Schema & High-Scale Design (50L+ Events)](#2-normalized-schema--high-scale-design-50l-events)
3. [Design Decisions & Assumptions under Ambiguity](#3-design-decisions--assumptions-under-ambiguity)
4. [Mid-Cycle Plan Changes & Segmented Proration (Requirement 8)](#4-mid-cycle-plan-changes--segmented-proration-requirement-8)
5. [Caching Strategy & Event Invalidation (Requirement 4)](#5-caching-strategy--event-invalidation-requirement-4)
6. [Queued Chunked Billing & Invoicing (Requirement 3)](#6-queued-chunked-billing--invoicing-requirement-3)
7. [High-Throughput Idempotency & Rate Limiting (Requirements 2 & 6)](#7-high-throughput-idempotency--rate-limiting-requirements-2--6)
8. [Merchant Dashboard & Churn Intelligence (Requirement 5)](#8-merchant-dashboard--churn-intelligence-requirement-5)
9. [Setup & Running Locally](#9-setup--running-locally)
10. [Test Suite Verification](#10-test-suite-verification)
11. [Trade-offs Made & What We'd Do Differently with More Time](#11-trade-offs-made--what-wed-do-differently-with-more-time)
12. [API Reference & cURL Examples](#12-api-reference--curl-examples)

---

## 1. Architecture & System Overview

The engine follows **Domain-Driven Design** and **Separation of Concerns**. Business logic, mathematical formulas, and caching layers reside in dedicated services rather than being crammed into controllers:

```
                  ┌──────────────────────────────────────────────┐
                  │              HTTP API / Web Client           │
                  └──────────────────────┬───────────────────────┘
                                         │
                         [Rate Limiter: throttle:usage]
                                         │
                    ┌────────────────────┴─────────────────────┐
                    │                                          │
             [POST /usage]                              [API / Web UI]
                    │                                          │
            ┌───────▼───────────┐                     ┌────────▼──────────┐
            │  UsageController  │                     │ DashboardController
            └───────┬───────────┘                     └────────┬──────────┘
                    │                                          │
            ┌───────▼───────────┐                     ┌────────▼──────────┐
            │   UsageService    │                     │DashboardMetricsSvc│
            └───────┬───────────┘                     └────────┬──────────┘
                    │                                          │
       ┌────────────┴────────────┐                    ┌────────┴──────────┐
       │                         │                    │  PlanCacheService │
       ▼                         ▼                    └────────┬──────────┘
 [usage_events]            [daily_usages]                      ▼
 (Raw Append-Only)         (Atomic Rollup Read-Model)   [Redis / Array Cache]
                                 ▲                             │
                                 │                             ▼
                     ┌───────────┴─────────────┐          [plans table]
                     │     BillingService      │
                     └───────────▲─────────────┘
                                 │
                     ┌───────────┴─────────────┐
                     │ ProcessCycleBillingJob  │
                     │  (Queued Chunked Batch) │
                     └─────────────────────────┘
```

### Core Services
- **`App\Services\UsageService`**: Idempotent event ingestion, deduplication, and atomic rollup updates via database-native upserts (`ON DUPLICATE KEY UPDATE` / `ON CONFLICT`).
- **`App\Services\BillingService`**: Proration ratios, segment-aware pricing, included allowance math, overage fee calculations, and transaction-safe invoice creation.
- **`App\Services\SubscriptionService`**: Subscription lifecycle, cycle advancement, and mid-cycle plan changes with active segment tracking.
- **`App\Services\PlanCacheService`**: Redis/Array plan and pricing lookups with automated model event invalidation hooks.
- **`App\Services\DashboardMetricsService`**: Computes MTD top 5 customers, projected cycle-end overage run-rates, and month-over-month churn risk (>50% drop).
- **`App\Jobs\ProcessCycleBillingJob`**: Queueable, chunked (`chunkById(100)`) cycle-end billing runner.

---

## 2. Normalized Schema & High-Scale Design (50L+ Events)

Detailed architectural scaling analysis is documented in [docs/SCHEMA_AND_SCALE_DESIGN.md](docs/SCHEMA_AND_SCALE_DESIGN.md).

### Entity Relationship Model

| Table | Purpose | Key Indexes |
|---|---|---|
| `merchants` | Multi-tenant tenant root | `slug` (UNIQUE) |
| `plans` | Pricing tiers, cycles, allowances & overage rates | `[merchant_id, is_active]`, `[merchant_id, code]` (UNIQUE) |
| `customers` | End-users belonging to a merchant | `[merchant_id, email]` (UNIQUE), `[merchant_id, external_id]` |
| `subscriptions` | Active contract and billing cycle bounds | `[merchant_id, status]`, `[customer_id, status]`, `[current_cycle_end, status]` |
| `subscription_segments` | Normalized plan periods for mid-cycle changes | `[subscription_id, starts_at, ends_at]`, `[subscription_id, billing_cycle_start]` |
| `usage_events` | Append-only raw event audit log | `idempotency_key` (UNIQUE), `[merchant_id, customer_id, recorded_at]` |
| `daily_usages` | Pre-aggregated materialized rollup read-model | `[merchant_id, customer_id, metric, usage_date]` (UNIQUE), `[customer_id, usage_date]` |
| `invoices` & `items` | Finalized billing statements & line items | `[merchant_id, status]`, `[customer_id, period_end]`, `[invoice_id, type]` |

### Scaling to 50 Lakhs (5,000,000+) Usage Event Rows

1. **Memory & Buffer Pool Sizing**:
   - Average row size: ~112 bytes.
   - 5,000,000 rows $\approx$ 560 MB raw data + 610 MB index trees $\approx$ **1.17 GB**.
   - An InnoDB buffer pool of 2–4 GB keeps 100% of the active working set in RAM.
2. **Why Querying Raw Logs at Cycle-End Fails**:
   - Querying `usage_events` directly requires full B-Tree scans across millions of rows ($O(N)$), causing lock contention and query timeouts.
3. **The Solution: Pre-Aggregated `daily_usages` Read-Model**:
   - On event ingest, an atomic upsert increments `daily_usages` for `[merchant_id, customer_id, metric, usage_date]`.
   - A customer generating 100,000 raw events in a month produces only **30 rows** in `daily_usages`.
   - Cycle-end billing queries scan 30 rows instead of 100,000, reducing query latency from seconds to **< 1.5 ms**.


---

## 3. Design Decisions & Assumptions under Ambiguity

| Requirement / Scenario | Ambiguity / Question | Decision & Implementation Rationale |
|---|---|---|
| **Proration Math** | Should proration round up or down? How are days counted? | Calculated as `activeDays / totalCycleDays` rounded to 4 decimals (`0.0000`). Days are calculated using integer day boundaries or exact seconds divided by 86,400. |
| **Included Usage Proration** | Does allowance prorate mid-cycle or remain fixed? | Supported via boolean `plans.prorate_allowance`. If true: `allowance = round(included_units * proration_ratio)`. If false: full allowance applies. |
| **Mid-Cycle Plan Changes** | Does a change trigger immediate invoice or wait for cycle end? | The assignment states: *"usage recorded before change must be billed at original plan's rate, and after at new plan's rate, with proration reflecting both segments"*. Modeled as **split segments billed together at cycle end** (or previewed anytime). |
| **POST /usage Route** | What is the exact URL path? | Supported both top-level `POST /usage` and tenant-scoped `POST /api/v1/merchants/{merchant}/usage`. `merchant_id` can be passed in payload body or via `X-Merchant-Id` header. |
| **Projected Overage Revenue** | How is "projected" defined? | Accrued overage to date + run-rate linear projection to cycle end: `projected_units = (actual_units / elapsed_seconds) * total_cycle_seconds`. If run-rate projected units exceed allowance, future overage is forecast. |
| **Churn Risk (>50% Drop)** | Which window defines month-over-month? | Compares current calendar month units against previous calendar month units for each customer. Accounts with $>50\%$ drop are flagged with risk levels (`critical` if $\ge 80\%$, `high` if $>50\%$). |

---

## 4. Mid-Cycle Plan Changes & Segmented Proration (Requirement 8)

When a customer upgrades or downgrades mid-cycle (e.g. Day 12 of a 30-day cycle):
1. The active segment in `subscription_segments` is closed at `effective_at`.
2. A new segment is opened from `effective_at` through `current_cycle_end` with the new plan.
3. At cycle-end, `BillingService::calculateBillingDetails()` computes each segment independently:

$$\text{Segment Proration Ratio } R_i = \frac{\text{Active Days in Segment } i}{\text{Total Cycle Days}}$$

$$\text{Segment Base Fee } = \text{Base Price}_i \times R_i$$

$$\text{Segment Allowance } = \text{Included Units}_i \times R_i$$

$$\text{Segment Overage } = \max(0, \text{Segment Usage} - \text{Segment Allowance}) \times \text{Overage Rate}_i$$

$$\text{Total Invoice Amount} = \sum (\text{Segment Base Fee} + \text{Segment Overage})$$

### Concrete Verified Example
- **Starter Tier** ($30/mo, 1,000 units, $0.05 overage) $\rightarrow$ Active for 12 days ($40\%$):
  - Base fee: $\$30.00 \times 0.40 = \mathbf{\$12.00}$
  - Allowance: $1,000 \times 0.40 = 400$ units
  - Usage recorded: 600 units $\rightarrow$ Overage: 200 units $\times \$0.05 = \mathbf{\$10.00}$
  - Segment 1 Subtotal: $\$22.00$
- **Growth Tier** ($90/mo, 10,000 units, $0.02 overage) $\rightarrow$ Active for 18 days ($60\%$):
  - Base fee: $\$90.00 \times 0.60 = \mathbf{\$54.00}$
  - Allowance: $10,000 \times 0.60 = 6,000$ units
  - Usage recorded: 7,500 units $\rightarrow$ Overage: 1,500 units $\times \$0.02 = \mathbf{\$30.00}$
  - Segment 2 Subtotal: $\$84.00$
- **Invoice Line Items**: Itemized separately per plan segment, totaling **$\$106.00$**.

---

## 5. Caching Strategy & Event Invalidation (Requirement 4)

- **Driver**: Redis / Array / File Cache.
- **Cache Keys**:
  - `plans:merchant:{merchantId}` $\rightarrow$ Stores active plans collection.
  - `plans:id:{planId}` $\rightarrow$ Stores individual plan model.
- **TTL**: 3,600 seconds (1 hour).
- **Invalidation Strategy**:
  - Wired into [Plan.php](app/Models/Plan.php) model lifecycle hooks:
    ```php
    protected static function booted(): void
    {
        static::saved(fn(Plan $plan) => app(PlanCacheService::class)->invalidate($plan));
        static::deleted(fn(Plan $plan) => app(PlanCacheService::class)->invalidate($plan));
    }
    ```
  - Any plan update, price change, or plan deletion instantly purges the corresponding cache keys, ensuring 100% cache consistency without stale pricing.

---

## 6. Queued Chunked Billing & Invoicing (Requirement 3)

The billing job `App\Jobs\ProcessCycleBillingJob` handles cycle-end evaluation at scale:
- Evaluates active subscriptions where `current_cycle_end <= $asOf`.
- **Chunked Processing**: Uses `chunkById($chunkSize)` (default 100) to keep memory constant regardless of whether 100 or 100,000 subscriptions are due.
- **Transaction Safety**: Each subscription invoice and its line items are created within an atomic database transaction.
- **CLI Command**:
  ```bash
  # Process synchronously with table report
  php artisan billing:process-cycle

  # Dispatch as queued chunked background job
  php artisan billing:process-cycle --queue --chunk=100
  ```

---

## 7. High-Throughput Idempotency & Rate Limiting (Requirements 2 & 6)

### Idempotency Guarantee
- Every usage event can supply an `idempotency_key` (e.g. UUIDv4 or client request hash).
- If the key exists:
  - The API immediately returns `HTTP 200 OK` with the original event details.
  - **Zero double-counting**: No new record is inserted, and no increment occurs in `daily_usages`.
- If the key is new:
  - The API inserts into `usage_events` and atomically upserts into `daily_usages`, returning `HTTP 201 Created`.

### Rate Limiting
- Configured in [AppServiceProvider.php](app/Providers/AppServiceProvider.php) via `RateLimiter::for('usage', ...)`.
- Permits **600 requests per minute** per merchant/IP.
- Emits standard RFC rate limit headers:
  - `X-RateLimit-Limit: 600`
  - `X-RateLimit-Remaining: 599`

---

## 8. Merchant Dashboard & Churn Intelligence (Requirement 5)

Accessible via JSON API `GET /merchants/{id}/dashboard` and visualized in the interactive Blade UI:

```json
{
  "merchant_id": 1,
  "merchant_name": "Nexus Cloud Platform",
  "currency": "USD",
  "current_cycle_usage": {
    "used_units": 184320,
    "total_allowance": 250000,
    "formatted": "184,320 / 250,000 units",
    "percentage": 73.7
  },
  "active_plan": "Growth Tier — monthly",
  "top_customers": [
    {
      "customer_id": 1,
      "name": "Acme Corporation",
      "email": "devops@acme.corp",
      "plan_name": "Growth Tier",
      "total_usage_units": 12366,
      "allowance": 10000,
      "percentage_of_allowance": 124
    }
  ],
  "projected_overage_revenue": {
    "currency": "USD",
    "accrued_overage_amount": 436.57,
    "projected_overage_amount": 436.57,
    "active_subscriptions_count": 5,
    "subscriptions_with_overage_count": 4
  },
  "churn_risk_customers": [
    {
      "customer_id": 5,
      "name": "Stale Innovations",
      "email": "accounts@staleinno.com",
      "previous_month_usage": 8250,
      "current_month_usage": 450,
      "drop_percentage": 94.5,
      "risk_level": "critical"
    }
  ],
  "daily_usage_trend": [ ... 30 days of data ... ]
}
```

### UI Wireframe Implementation
The Blade view [dashboard.blade.php](resources/views/dashboard.blade.php) faithfully replicates the suggested wireframe layout:
- **Top 3 KPI Cards**: Current Cycle Usage progress, Projected Overage Revenue, and Active Plan.
- **Left Column**: Top 5 Customers by Usage table with `% of Allowance`, and 30-Day Daily Usage Trend SVG sparkline.
- **Right Column**: Churn Risk (`usage ↓ >50% MoM`) alert box and System Status informational panel.
- **Interactive Workbench**: Live metering cards, high-volume batch ingestion simulator, and one-click cycle-end billing runner.

---

## 9. Customer Self-Service Web Portal

Customers have access to a dedicated self-service portal:
- **URL**: `http://127.0.0.1:8000/portal/login`
- **Features**:
  - **1-Click Quick Demo Login**: Select any customer account from any merchant to log in instantly, or log in via email.
  - **Plan Purchase**: Unsubscribed customers can review tiers and subscribe immediately.
  - **Mid-Cycle Plan Upgrades & Downgrades**: Interactive modal with live proration calculations. Upgrades/downgrades apply immediately, creating segmented billing periods and prorated allowances.
  - **Live Usage Meter**: Dynamic progress bar indicating consumption against plan allowance, overage units, and estimated cycle bill.
  - **Invoice History**: Complete archive of past invoices with itemized charge breakdowns.

---

## 10. Setup & Running Locally

### Prerequisites
- PHP 8.2+ with `pdo_mysql`, `pdo_sqlite`, `curl`, `mbstring`
- Composer
- MariaDB or MySQL (or SQLite for testing)

### Installation
```bash
# 1. Clone repository
git clone <repo_url>
cd mallow

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seed rich demo dataset
php artisan migrate:fresh --seed

# 5. Start development server
php artisan serve
```

- Merchant Admin Console: **`http://127.0.0.1:8000`**
- Customer Self-Service Portal: **`http://127.0.0.1:8000/portal/login`**
- Merchant Self-Service Portal: **`http://127.0.0.1:8000/merchant/login`**

---

## 11. Test Suite Verification

Run the complete automated test suite:
```bash
php artisan test
```

### Test Coverage Results (32 Tests, 170 Assertions)
```
   PASS  Tests\Unit\BillingProrationTest
  ✓ proration ratio calculation for full and mid cycle                                   1.83s  
  ✓ full cycle billing with no overage                                                   0.08s  
  ✓ mid cycle start prorates base price and included units                               0.04s  
  ✓ unprorated allowance option                                                          0.03s  

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                    0.01s  

   PASS  Tests\Unit\MidCyclePlanChangeTest
  ✓ mid cycle upgrade calculates segmented proration and overage                         0.06s  
  ✓ mid cycle downgrade with zero overage                                                0.05s  

   PASS  Tests\Feature\BillingCycleTest
  ✓ generate invoice for mid cycle subscription with overage                             0.16s  
  ✓ automated process cycle bills only due subscriptions                                 0.04s  
  ✓ tenant isolation guards                                                              0.05s  

   PASS  Tests\Feature\CustomerPortalTest
  ✓ login page renders successfully                                                      0.06s  
  ✓ login with valid email authenticates customer                                        0.05s  
  ✓ login with demo customer id                                                          0.03s  
  ✓ unauthenticated user redirected from portal dashboard                                0.03s  
  ✓ authenticated customer can view dashboard with no subscription                       0.04s  
  ✓ customer can purchase plan when unsubscribed                                         0.09s  
  ✓ cannot purchase plan if already active                                               0.03s  
  ✓ customer can upgrade plan mid cycle                                                  0.09s  
  ✓ customer can downgrade plan mid cycle                                                0.07s  
  ✓ customer logout                                                                      0.03s  

   PASS  Tests\Feature\DashboardEndpointTest
  ✓ merchant dashboard returns required metrics                                          0.11s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                        0.03s  

   PASS  Tests\Feature\MidCycleBillingTest
  ✓ end to end mid cycle plan change and invoice generation                              0.09s  

   PASS  Tests\Feature\PlanCacheTest
  ✓ plan lookups are cached and invalidated on change                                    0.03s  

   PASS  Tests\Feature\QueuedBillingJobTest
  ✓ queued chunked job processes due subscriptions                                       0.09s  
  ✓ artisan command can dispatch job to queue                                            0.02s  

   PASS  Tests\Feature\UsageIngestionTest
  ✓ single usage event ingestion and daily rollup                                        0.04s  
  ✓ idempotency deduplication prevents duplicate billing                                 0.07s  
  ✓ batch ingestion with mixed dates and duplicates                                      0.03s  
  ✓ customer usage summary endpoint                                                      0.03s  

   PASS  Tests\Feature\UsageRateLimitingTest
  ✓ post usage is idempotent and does not double count                                   0.08s  
  ✓ usage endpoint has rate limiting headers                                             0.05s  
  Tests:    32 passed (170 assertions)
  Duration: 3.74s
```

## 12. API Reference & cURL Examples

### 1. Ingest Single Usage Event (`POST /usage`)
```bash
curl -X POST http://127.0.0.1:8000/usage \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "merchant_id": 1,
    "customer_id": 1,
    "units": 150,
    "metric": "api_calls",
    "idempotency_key": "req_unique_839210"
  }'
```

### 2. Get Merchant Dashboard Intelligence (`GET /merchants/{id}/dashboard`)
```bash
curl -X GET http://127.0.0.1:8000/merchants/1/dashboard \
  -H "Accept: application/json"
```

### 3. Change Plan Mid-Cycle (`POST /merchants/{m}/subscriptions/{s}/change-plan`)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/merchants/1/subscriptions/1/change-plan \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "plan_id": 2,
    "effective_at": "2026-09-15 00:00:00"
  }'
```

### 4. Process Cycle Billing via CLI
```bash
# Direct execution with ASCII breakdown table
php artisan billing:process-cycle

# Queued background chunked dispatch
php artisan billing:process-cycle --queue --chunk=100
```

# AI-Assisted Development & Prompt Log

> **Mallow Technologies — Senior Laravel Developer Assignment**  
> In accordance with the assignment brief, this document logs all AI-assisted prompts, architectural guidance, and iterative workflows used during the development of the Subscription Billing & Usage-Metering System.

---

## Prompt 1: Initial Troubleshooting & Environment Setup
- **Context**: Running `php artisan migrate` failed with:
  `SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'users' already exists`
- **Prompt**:
  > User pasted terminal output: `php artisan migrate` failure on `users` table.
- **Analysis & Action**:
  - Investigated database tables; identified orphaned tables with empty migrations table.
  - Executed `php artisan migrate:fresh`.
  - Identified database dialect incompatibility in `UsageService::applyDailyRollup()` where PostgreSQL/SQLite `ON CONFLICT` failed on MySQL/MariaDB.
  - Replaced with driver-agnostic logic supporting `ON DUPLICATE KEY UPDATE` on MySQL/MariaDB and `ON CONFLICT` on SQLite.
  - Enabled `RefreshDatabase` in `ExampleTest.php` and verified all baseline tests passed.

---

## Prompt 2: Full Functional Requirements & Architecture Planning
- **Prompt**:
  > User provided the 8 core functional requirements:
  > 1. Normalized, indexed schema with 50L+ rows scaling analysis and partitioning.
  > 2. Safe, high-throughput, idempotent `POST /usage` endpoint.
  > 3. Queued, chunked job for daily usage aggregation & cycle-end invoicing with proration/overage math.
  > 4. Plan/pricing caching with invalidation strategy.
  > 5. `GET /merchants/{id}/dashboard` returning top 5 customers, projected overage revenue, and churn risk (>50% drop).
  > 6. Basic rate-limiting on the usage endpoint.
  > 7. Automated tests for aggregation, proration, and overage edge cases.
  > 8. Mid-cycle plan upgrade/downgrade handling with segment proration.
  > + Suggested UI Reference (Wireframe).
- **Analysis & Implementation Plan**:
  - Formulated an architectural implementation plan in `implementation_plan.md` adhering to Domain-Driven Design and Separation of Concerns.
  - Designed the `subscription_segments` relational model for mid-cycle plan changes.
  - Outlined mathematical formulation for multi-segment cycle proration.
  - Created scaling architecture document `docs/SCHEMA_AND_SCALE_DESIGN.md`.

---

## Prompt 3: Implementation & Execution
- **Step 3.1: Schema & Scaling Documentation**
  - Documented InnoDB buffer pool memory sizing for 5,000,000 rows (~1.17 GB RAM).
  - Designed MySQL range partitioning by `recorded_at` (`TO_DAYS`) for instant metadata-only partition drops ($O(1)$ retention).
  - Documented materialized rollup read-model (`daily_usages`) reducing cycle-end billing queries from $O(N)$ to $O(D)$ ($< 1.5\text{ ms}$).
- **Step 3.2: Mid-Cycle Plan Upgrades & Downgrades (Requirement 8)**
  - Migration: `2026_09_13_000007_create_subscription_segments_table.php`.
  - Model: `App\Models\SubscriptionSegment`.
  - Enhanced `SubscriptionService::changePlan()` to close active segments and open new ones.
  - Enhanced `BillingService::calculateBillingDetails()` to compute segment proration ratios, segment allowances, segment usage, and segment overage at individual plan rates.
- **Step 3.3: High-Throughput Idempotent `POST /usage` & Rate Limiting (Requirements 2 & 6)**
  - Route: `POST /usage`, `POST /api/usage`, `POST /api/v1/usage`.
  - Implemented `UsageController::ingestDirect()` with idempotency key deduplication (returns HTTP 200 on retry without double-counting).
  - Configured `throttle:usage` rate limiter (600 req/min per merchant/IP) emitting RFC headers.
- **Step 3.4: Queued Chunked Billing Job (Requirement 3)**
  - Implemented `App\Jobs\ProcessCycleBillingJob` using `chunkById(100)` for constant memory processing.
  - Updated `ProcessBillingCycleCommand` to support `--queue` and `--chunk` flags.
- **Step 3.5: Plan Caching & Invalidation (Requirement 4)**
  - Implemented `PlanCacheService` caching plan collections and single plan lookups with 1-hour TTL.
  - Hooked into `Plan::saved` and `Plan::deleted` events for automated cache eviction.
- **Step 3.6: Dashboard API & Wireframe UI (Requirement 5)**
  - Implemented `DashboardMetricsService`:
    - Top 5 customers by usage with `% of Allowance`.
    - Projected overage revenue (accrued + linear run-rate projection to cycle end).
    - Churn risk detection (>50% MoM drop).
    - 30-day daily usage trend sparkline data.
  - Registered `GET /merchants/{id}/dashboard`.
  - Upgraded `dashboard.blade.php` to faithfully mirror the wireframe layout.
- **Step 3.7: Automated Test Suite (Requirement 7)**
  - Authored 22 automated unit and feature tests with 128 assertions across all edge cases.
  - Verified 100% pass rate (`22 passed, 128 assertions`).

---

## Prompt 4: PDF Brief & Wireframe Layout Verification
- **Prompt**:
  > User uploaded the official PDF brief containing the Wireframe layout reference and submission guidelines.
- **Analysis & Action**:
  - Matched every wireframe detail:
    - 3 Top Cards: Current Cycle Usage (`184,320 / 250,000 units`), Projected Overage Revenue (`₹ 42,600`), and Active Plan (`Growth — monthly`).
    - Two-column layout: Top 5 Customers by Usage table with `% of Allowance`, 30-Day Daily Usage Trend SVG sparkline, Churn Risk alert box, and System Status panel.
  - Authored production-ready `README.md` with team-ready architecture summary, assumptions under ambiguity, and trade-offs.
  - Authored `prompts/PROMPT_LOG.md` for full review transparency.

---

## Prompt 5: Customer Web Portal for Plan Purchase, Upgrades & Downgrades
- **Prompt**:
  > "need customer login for purchase plan, upgrade plan or downgrade plans in web"
- **Analysis & Action**:
  - Implemented Customer Portal web routes (`/portal/login`, `/portal/logout`, `/portal`, `/portal/purchase`, `/portal/change-plan`).
  - Created `CustomerPortalController` handling session-based customer authentication, plan purchasing, and mid-cycle plan upgrades and downgrades via `SubscriptionService`.
  - Created customer login UI (`resources/views/portal/login.blade.php`) featuring a 1-click quick-select demo switcher across merchants and custom email login.
  - Created customer portal dashboard (`resources/views/portal/dashboard.blade.php`) with:
    - Active plan status, days remaining, live usage gauge progress bar, and projected cycle bill.
    - Mid-cycle segment timeline if plan was changed mid-cycle.
    - Available plans catalog with context-aware buttons (Current Plan badge, Upgrade green button, Downgrade amber button, Purchase button).
    - Interactive plan change confirmation modal with proration preview.
    - Invoices table with itemized breakdown modal.
  - Added "Customer Portal" quick-access navigation link in the header of the Merchant Admin Console (`resources/views/dashboard.blade.php`).
  - Created feature test suite `tests/Feature/CustomerPortalTest.php` testing login, authentication checks, plan purchase, mid-cycle upgrades with segment creation, downgrades, and logout.
  - All 32 automated tests (170 assertions) pass with 100% success rate.

---

## Prompt 6: Merchant Portal for Plan Definition & Management
- **Prompt**:
  > "merchant portal for Merchants (tenants), each defining one or more Plans (name, base price, billing cycle, included usage units, overage rate per unit)."
- **Analysis & Action**:
  - Implemented `MerchantPlanController` with `store`, `update`, and `toggle` actions.
  - Created web routes:
    - `POST /merchants/{merchant}/plans` (store new plan)
    - `POST /merchants/{merchant}/plans/{plan}/update` (update plan parameters)
    - `POST /merchants/{merchant}/plans/{plan}/toggle` (toggle plan active/archived status)
  - Enhanced the **Plans & Pricing** tab in `resources/views/dashboard.blade.php`:
    - Added "+ Define New Plan" button and interactive Plan Creation Modal with validation for all 5 required specifications: `name`, `base_price`, `billing_cycle`, `included_usage_units`, and `overage_rate_per_unit`.
    - Added Plan Edit Modal pre-populated with existing values.
    - Added Active Subscribers counter badge, status badge, and 1-click status toggle button.
  - Implemented `tests/Feature/MerchantPlanManagementTest.php` testing plan creation, input validation, update, status toggling, tenant isolation, and immediate live synchronization with the Customer Portal.
  - Full automated test suite passes: **38 tests, 197 assertions** (100% success).

---

## Prompt 7: Dedicated Merchant Portal (Login, Dashboard & New Plan Definition)
- **Prompt**:
  > "need merchnat portal for login, dashboard and new plan"
- **Analysis & Action**:
  - Implemented dedicated session-based authentication for merchants (`/merchant/login`, `/merchant/logout`) with 1-click tenant selector for seeded accounts (*Nexus Cloud Platform*, *Apex AI Solutions*) and email input.
  - Implemented authenticated Merchant Portal dashboard (`/merchant`) in `MerchantPortalController`:
    - Tenant-isolated header with merchant branding, email, currency badge (USD/EUR), timezone, customer portal link, and logout.
    - 4 Key KPI Cards: Current Cycle Usage with visual progress bar, Projected Overage Revenue with run-rate model, Active Plans count, and Subscribed Customers count.
    - Tab 1 (Overview & Analytics): Top 5 Customers by Usage table (Req 5a), 30-Day Daily Usage Sparkline Trend, and Month-over-Month Churn Risk Alert (>50% drop, Req 5c).
    - Tab 2 (Plans Catalog): Grid of merchant plans with pricing, included units, overage rate, subscriber count, and active/archive status toggle (`POST /merchant/plans/{plan}/toggle`).
    - Tab 3 (Customer Subscriptions): Live metering table with billing cycles and proration status.
    - Tab 4 (Invoices History): Complete invoice ledger with base fees and overage charges.
    - Tab 5 & Modal (Define New Plan): Web form to define plans with `name`, `code`, `base_price`, `billing_cycle`, `cycle_days`, `included_usage_units`, `overage_rate_per_unit`, and proration toggle, submitting to `POST /merchant/plans`.
  - Built comprehensive feature test suite `tests/Feature/MerchantPortalTest.php` covering:
    - Login rendering and authentication via email or tenant ID.
    - Unauthenticated redirect protection.
    - Dashboard metrics, KPIs, and plan list rendering.
    - Plan definition with slug generation and validation.
    - Status toggle and tenant isolation enforcement.
    - Immediate synchronization of new plans with the Customer Portal.
    - Logout session clearance.
  - Full automated test suite passes: **51 tests, 248 assertions** (100% success rate across the entire application).

---

## Prompt 8: Password Authentication (123456 Default) & Self-Service Password Change
- **Prompt**:
  > "need merchant and cutomers login with password as 123456 in their portal also need password change option"
- **Analysis & Action**:
  - Created migration `2026_09_13_000008_add_password_to_merchants_and_customers_table.php` adding `password` column to both `merchants` and `customers` tables, setting default bcrypt hash of `'123456'` for all existing rows.
  - Updated models `Merchant` and `Customer`: added `password` to `$fillable`, added `$hidden = ['password']`, and in `booted()` automatically set bcrypt hash of `'123456'` if empty during creation.
  - Updated web routes in `routes/web.php`:
    - `POST /merchant/change-password` -> `MerchantPortalController::updatePassword`
    - `POST /portal/change-password` -> `CustomerPortalController::updatePassword`
  - Updated controllers `MerchantPortalController` and `CustomerPortalController`:
    - `login()`: validates password against database hash using `Hash::check()`. Returns descriptive error if incorrect.
    - `updatePassword()`: validates current password via `Hash::check()`, verifies `min:6|confirmed` on new password, updates bcrypt hash, and redirects with success feedback.
  - Updated views:
    - `merchant/login.blade.php`: added password input field (defaulted to `123456`) and passed `123456` in 1-click select forms.
    - `portal/login.blade.php`: added password input field (defaulted to `123456`) and passed `123456` in 1-click customer select forms.
    - `merchant/dashboard.blade.php`: added "Change Password" button in top navigation and interactive Change Password modal.
    - `portal/dashboard.blade.php`: added "Change Password" button in top navigation and interactive Change Password modal.
  - Added feature tests in `tests/Feature/MerchantPortalTest.php` and `tests/Feature/CustomerPortalTest.php` testing:
    - Login with correct password (`123456`) succeeds.
    - Login with incorrect password fails with validation errors.
    - Changing password via modal updates hash in DB.
    - Logging in with old password fails after change.
    - Logging in with new password succeeds.
    - Incorrect current password fails validation.
  - Full automated test suite passes: **60 tests, 280 assertions** (100% pass rate in 2.24s).



# Multi-Tenant SaaS Metering & Cycle-End Billing: Schema & High-Scale Architecture Design

## Executive Summary
This document details the normalized, indexed relational database schema and scaling architecture for the SaaS Metering and Billing engine. It provides an explicit technical evaluation of how the database architecture handles high-velocity write workloads of **50 Lakhs (5,000,000+) usage-event rows**, and outlines our partitioning, indexing, caching, and denormalization strategies.

---

## 1. Normalized Relational Schema Design

The system implements a multi-tenant relational model maintaining referential integrity, tenant isolation, and auditability:

```
[merchants] (Tenant Root)
   │
   ├──< [plans] (Pricing Tiers, Allowances, Overage Rates)
   │      │
   ├──< [customers] (End-Users of Tenant)
   │      │
   │      └──< [subscriptions] (Active Contract / Lifecycle)
   │             │
   │             ├──< [subscription_segments] (Mid-Cycle Plan Variations)
   │             │
   │             └──< [invoices] ──< [invoice_items]
   │
   ├──< [usage_events] (Append-Only Raw Ingestion Log)
   │
   └──< [daily_usages] (Pre-Aggregated Read-Model Rollups)
```

### Table Definitions & Indexing Rationale

1. **`merchants`**
   - **Fields**: `id`, `name`, `slug`, `email`, `currency`, `timezone`, `timestamps`
   - **Indexes**: `slug` (UNIQUE)

2. **`plans`**
   - **Fields**: `id`, `merchant_id`, `name`, `code`, `base_price`, `cycle_days`, `included_usage_units`, `overage_rate_per_unit`, `prorate_allowance`, `is_active`, `timestamps`
   - **Indexes**: `['merchant_id', 'is_active']`, `['merchant_id', 'code']` (UNIQUE)

3. **`customers`**
   - **Fields**: `id`, `merchant_id`, `name`, `email`, `external_id`, `timestamps`
   - **Indexes**: `['merchant_id', 'email']` (UNIQUE), `['merchant_id', 'external_id']`

4. **`subscriptions`**
   - **Fields**: `id`, `merchant_id`, `customer_id`, `plan_id`, `status`, `starts_at`, `current_cycle_start`, `current_cycle_end`, `cancelled_at`, `timestamps`
   - **Indexes**: `['merchant_id', 'status']`, `['customer_id', 'status']`, `['current_cycle_end', 'status']` (for high-speed batch billing candidate lookups)

5. **`subscription_segments`** (Handles Mid-Cycle Plan Upgrades / Downgrades)
   - **Fields**: `id`, `subscription_id`, `plan_id`, `starts_at`, `ends_at`, `billing_cycle_start`, `billing_cycle_end`, `timestamps`
   - **Indexes**: `['subscription_id', 'starts_at', 'ends_at']`
   - **Purpose**: Normalizes multiple plan tenures within a single billing cycle. When a customer upgrades or downgrades, the current segment is sealed and a new segment opened. Usage within each segment interval is billed at that segment's specific plan rate and prorated allowance.

6. **`usage_events`** (Raw High-Volume Event Log)
   - **Fields**: `id` (BIGINT AUTO_INCREMENT), `merchant_id`, `customer_id`, `metric`, `units`, `idempotency_key` (VARCHAR 128 UNIQUE), `recorded_at` (DATETIME), `created_at` (TIMESTAMP)
   - **Indexes**:
     - `idempotency_key` (UNIQUE) — guarantees at-most-once delivery and zero double-counting on network retries.
     - `['merchant_id', 'customer_id', 'recorded_at']` (Composite B-Tree) — enables tenant-scoped time-range lookups.

7. **`daily_usages`** (Atomic Rollup Read-Model)
   - **Fields**: `id`, `merchant_id`, `customer_id`, `metric`, `usage_date` (DATE), `total_units` (BIGINT), `event_count` (BIGINT), `last_recorded_at` (DATETIME), `timestamps`
   - **Indexes**:
     - `['merchant_id', 'customer_id', 'metric', 'usage_date']` (UNIQUE) — enables single-query atomic upserts (`ON DUPLICATE KEY UPDATE` / `ON CONFLICT`).
     - `['customer_id', 'usage_date']` — powers billing period aggregation in $O(\text{days})$ instead of scanning millions of raw rows.

8. **`invoices` & `invoice_items`**
   - **Fields**: `invoice_number`, `merchant_id`, `customer_id`, `subscription_id`, `period_start`, `period_end`, `base_amount`, `overage_amount`, `total_amount`, `proration_ratio`, `status`, `currency`, `issued_at`
   - **Indexes**: `['merchant_id', 'status']`, `['customer_id', 'period_end']`, `['invoice_id', 'type']`

---

## 2. Scaling Analysis: 50 Lakhs (5,000,000+) Usage Event Rows

### A. Mathematical Sizing & Buffer Pool Footprint
For 5,000,000 raw usage event records:
- **Row Size Breakdown**:
  - `id` (BIGINT): 8 bytes
  - `merchant_id` + `customer_id`: 8 + 8 = 16 bytes
  - `metric` (VARCHAR 50): ~15 bytes average
  - `units` (BIGINT): 8 bytes
  - `idempotency_key` (VARCHAR 128): ~40 bytes average
  - `recorded_at` + `created_at`: 8 + 4 = 12 bytes
  - InnoDB Record Overhead: 13 bytes
  - **Total Raw Data per Row**: ~112 bytes
- **Data Footprint**: $5,000,000 \times 112\text{ bytes} \approx 560\text{ MB}$
- **Secondary Index Footprint**:
  - Unique index on `idempotency_key`: ~280 MB
  - Composite index on `(merchant_id, customer_id, recorded_at)`: ~210 MB
  - Clustered primary index: ~120 MB
  - **Total Index Overhead**: ~610 MB
- **Total Storage on Disk**: $\approx 1.17\text{ GB}$

### B. Write Amplification & High-Throughput Ingestion
1. **The Write Dilemma**:
   At 50L+ rows, writing each event directly to the database with multiple secondary indexes causes random I/O (especially on random UUID idempotency keys) and lock contention on B-Tree leaf pages.
2. **Mitigations Implemented & Recommended**:
   - **Ordered Primary Key**: Uses 64-bit BIGINT auto-increment (or monotonic ULID/UUIDv7) ensuring sequential append to the clustered index without page splits.
   - **Pre-Aggregated Materialized Rollup (`daily_usages`)**:
     By rolling up events on ingest (or via stream micro-batches) into `daily_usages`, a customer with 100,000 events in a month results in only **30 rows** in `daily_usages`.
   - **Cycle-End Query Complexity**:
     - *Querying raw table*: $O(N)$ scanning up to 5,000,000 rows.
     - *Querying daily rollup*: $O(D)$ scanning at most 30 to 31 rows per customer cycle. Query time drops from seconds to $< 1.5\text{ ms}$.

---

## 3. Partitioning Strategy

When scaling from 50 Lakhs (5M) to 10+ Crore (100M+) rows, single-table InnoDB performance degrades during index reorganization and VACUUM/OPTIMIZE maintenance.

### Range Partitioning on `recorded_at`
```sql
ALTER TABLE usage_events PARTITION BY RANGE (TO_DAYS(recorded_at)) (
    PARTITION p_2026_07 VALUES LESS THAN (TO_DAYS('2026-08-01')),
    PARTITION p_2026_08 VALUES LESS THAN (TO_DAYS('2026-09-01')),
    PARTITION p_2026_09 VALUES LESS THAN (TO_DAYS('2026-10-01')),
    PARTITION p_2026_10 VALUES LESS THAN (TO_DAYS('2026-11-01')),
    PARTITION p_future  VALUES LESS THAN MAXVALUE
);
```

#### Why Partition by Month?
1. **Partition Pruning**: Queries for a specific month (e.g., September 2026) scan *only* the active month's partition (`p_2026_09`). The remaining 80%+ of the table data and indexes are completely skipped by the query optimizer.
2. **Instant Zero-Cost Data Retention (Drop Partition)**:
   In standard SQL, running `DELETE FROM usage_events WHERE recorded_at < NOW() - INTERVAL 90 DAY` on 50L rows locks tables, floods redo logs, causes replication lag, and fragments InnoDB disk tablespaces. With partitioning, expiring old data is an instant metadata operation:
   ```sql
   ALTER TABLE usage_events DROP PARTITION p_2026_07;
   ```
   This reclaims gigabytes of disk space in $< 50\text{ ms}$ with zero lock contention.

---

## 4. Denormalization & Streaming Architecture (Targeting 500k+ req/sec)

```
[Clients]
   │
   ▼
[API Gateway (Rate Limiting: 600 req/min/tenant)]
   │
   ▼
[Kafka / AWS Kinesis / Redis Stream Buffer]
   │
   ├── (Worker Group 1) ──> [Batch Insert 1000s into usage_events]
   │
   └── (Stream Aggregator) ──> [Atomic Upsert into daily_usages]
```

1. **Ingestion Buffer**: Decouple HTTP ingestion from synchronous MySQL writes using an in-memory queue (Redis Streams or Apache Kafka). The API responds in $< 5\text{ ms}$ with an accepted receipt.
2. **Micro-Batch Flushing**: Background workers consume events from the stream and execute chunked multi-row inserts (`INSERT INTO usage_events VALUES (...), (...), ... [500 rows]`).
3. **Cold Storage Offload**: Raw event rows older than 90 days are extracted into columnar storage (AWS S3 + Parquet / ClickHouse / Snowflake) for compliance, dispute audits, and ML models, keeping the OLTP relational database lean and performant.

---

## 5. Plan Caching & Invalidation Strategy

Plan lookups occur on every billing calculation, dashboard query, and subscription verification.

- **Cache Layer**: Redis / In-Memory Array Cache (`Cache::remember`).
- **Keys**:
  - `plans:merchant:{merchantId}` (Stores collection of active plans for a merchant)
  - `plans:id:{planId}` (Stores serialized Plan model)
- **TTL**: 3600 seconds (1 hour).
- **Invalidation Strategy**:
  - **Event-Driven Write-Through / Eviction**:
    `Plan::saved` and `Plan::deleted` model events fire an invalidation hook via `PlanCacheService`:
    ```php
    Cache::forget("plans:merchant:{$plan->merchant_id}");
    Cache::forget("plans:id:{$plan->id}");
    ```
  - **Cache Stampede Prevention**: Lookups use atomic lock acquisition (`Cache::lock`) when rebuilding stale keys, preventing thundering herds on cache miss.

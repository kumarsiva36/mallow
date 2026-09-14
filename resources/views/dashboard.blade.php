<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Multi-Tenant SaaS Metering and Cycle-End Billing Engine with Proration and High-Volume Ingestion.">
    <title>{{ $currentMerchant ? $currentMerchant->name . ' — SaaS Metering & Billing' : 'SaaS Metering & Billing Engine' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0d14;
            --bg-secondary: #111622;
            --bg-card: rgba(22, 28, 45, 0.7);
            --bg-card-hover: rgba(30, 38, 60, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(99, 102, 241, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --accent-glow: rgba(99, 102, 241, 0.25);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 6px;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 15% 10%, rgba(99, 102, 241, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 85% 90%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .container {
            max-width: 1380px;
            margin: 0 auto;
            padding: 32px 24px 80px;
        }

        /* Top Header */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 36px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 20px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 8px 24px var(--accent-glow);
        }

        h1 {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .tenant-switcher {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg-secondary);
            padding: 8px 16px;
            border-radius: 40px;
            border: 1px solid var(--border-color);
        }

        .tenant-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }

        .tenant-select {
            background: transparent;
            color: #fff;
            font-family: var(--font-sans);
            font-size: 14px;
            font-weight: 600;
            border: none;
            outline: none;
            cursor: pointer;
        }

        .tenant-select option {
            background: var(--bg-secondary);
            color: #fff;
        }

        /* Metric Highlights */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, border-color 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-highlight);
        }

        .stat-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 32px;
            font-weight: 700;
            color: #fff;
        }

        .stat-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* Wireframe Reference Section */
        .wireframe-container {
            background: rgba(13, 17, 28, 0.9);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            width: 100%;
        }

        .wireframe-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1024px) {
            .wireframe-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Navigation Tabs */
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            overflow-x: auto;
            clear: both;
            width: 100%;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-family: var(--font-sans);
            font-size: 14px;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tab-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .tab-btn.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.2));
            border: 1px solid var(--border-highlight);
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Subscriptions Grid */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 700;
        }

        .subscriptions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 24px;
        }

        .sub-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s;
        }

        .sub-card:hover {
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .sub-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .sub-customer-name {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        .sub-plan-badge {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-prorated {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.4);
        }

        .badge-full {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .badge-due {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        /* Meter Progress */
        .meter-container {
            margin: 16px 0;
            background: rgba(0, 0, 0, 0.3);
            border-radius: var(--radius-sm);
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .meter-label-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .progress-bar-bg {
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-normal {
            background: linear-gradient(to right, #10b981, #3b82f6);
        }

        .progress-overage {
            background: linear-gradient(to right, #f59e0b, #ef4444);
        }

        .billing-preview-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: rgba(0, 0, 0, 0.25);
            padding: 12px;
            border-radius: var(--radius-sm);
            margin-top: 14px;
            font-size: 13px;
        }

        .preview-item-label {
            color: var(--text-muted);
            font-size: 11px;
        }

        .preview-item-val {
            font-weight: 600;
            color: #fff;
        }

        .preview-total {
            grid-column: span 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed rgba(255, 255, 255, 0.1);
            padding-top: 8px;
            margin-top: 4px;
            font-size: 14px;
            font-weight: 700;
            color: #a5b4fc;
        }

        .sub-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-family: var(--font-sans);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #fff;
            box-shadow: 0 4px 14px var(--accent-glow);
        }

        .btn-primary:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Simulator Forms */
        .simulator-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
        }

        .sim-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: #fff;
            font-family: var(--font-sans);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--accent-primary);
        }

        .benchmark-box {
            margin-top: 16px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: var(--radius-sm);
            padding: 14px;
            border-left: 3px solid var(--accent-primary);
            font-family: var(--font-mono);
            font-size: 12px;
            color: #93c5fd;
            display: none;
        }

        /* Tables */
        .table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            background: rgba(0, 0, 0, 0.4);
            text-align: left;
            padding: 14px 18px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-primary);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            background: #111726;
            border: 1px solid var(--border-highlight);
            border-radius: var(--radius-lg);
            max-width: 680px;
            width: 100%;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
        }

        .invoice-bill-to {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            padding: 16px;
            background: rgba(0, 0, 0, 0.25);
            border-radius: var(--radius-sm);
        }

        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1e293b;
            color: #fff;
            padding: 14px 22px;
            border-radius: var(--radius-md);
            border: 1px solid var(--accent-primary);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 2000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <header>
        <div class="brand-section">
            <div class="brand-logo">M</div>
            <div>
                <h1>Metering & Billing Engine</h1>
                <div class="subtitle">Multi-Tenant SaaS Usage Ingestion, Proration & Automated Cycle Billing</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
            <a href="{{ route('portal.login') }}" class="btn btn-secondary" style="border-radius: 30px; padding: 8px 18px; border: 1px solid rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.15); color: #e0e7ff; font-weight: 600;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Customer Portal
            </a>
            <div class="tenant-switcher">
                <span class="tenant-label">Active Merchant:</span>
                <select id="merchant-selector" class="tenant-select" onchange="switchMerchant(this.value)">
                    @foreach($merchants as $m)
                        <option value="{{ $m->id }}" {{ $currentMerchant && $currentMerchant->id === $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->currency }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #6ee7b7; padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 500;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #fca5a5; padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 13px;">
            <div style="font-weight: 700; margin-bottom: 4px;">Please correct the following errors:</div>
            <ul style="padding-left: 20px; margin: 0;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($currentMerchant)
    <!-- Quick Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Active Subscriptions</div>
            <div class="stat-value">{{ count($subscriptions) }}</div>
            <div class="stat-sub">Scored against custom plans</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Total Usage Ingested (Cycle)</div>
            <div class="stat-value">{{ number_format($totalUnitsCycle) }}</div>
            <div class="stat-sub">Units recorded & daily-aggregated</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Generated Invoices</div>
            <div class="stat-value">{{ count($invoices) }}</div>
            <div class="stat-sub">Itemized with proration & overage</div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Billed Revenue</div>
            <div class="stat-value">{{ $currentMerchant->currency }} {{ number_format($totalBilled, 2) }}</div>
            <div class="stat-sub">Base plan fees + usage overage</div>
        </div>
    </div>

    <!-- Suggested UI Reference: Merchant Dashboard Wireframe Section -->
    <div class="wireframe-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
            <h2 style="font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: #fff;">
                Merchant Dashboard — {{ $currentMerchant->name }}
            </h2>
            <span style="font-size: 11px; font-family: var(--font-mono); color: var(--text-muted); background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 4px;">
                WIREFRAME REFERENCE IMPLEMENTATION
            </span>
        </div>

        <!-- Wireframe 3 Top KPI Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <!-- KPI 1: Current Cycle Usage -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Current Cycle Usage</div>
                <div style="font-size: 22px; font-weight: 800; font-family: var(--font-mono); color: #fff;">
                    {{ $metrics['current_cycle_usage']['formatted'] }}
                </div>
                <div class="progress-bar-bg" style="margin-top: 10px; height: 6px;">
                    <div class="progress-bar-fill progress-normal" style="width: {{ $metrics['current_cycle_usage']['percentage'] }}%"></div>
                </div>
            </div>

            <!-- KPI 2: Projected Overage Revenue -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-highlight); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-size: 12px; font-weight: 600; color: #a5b4fc; margin-bottom: 8px;">Projected Overage Revenue</div>
                <div style="font-size: 24px; font-weight: 800; font-family: var(--font-mono); color: #818cf8;">
                    {{ $currentMerchant->currency === 'INR' ? '₹' : $currentMerchant->currency }} {{ number_format($metrics['projected_overage_revenue']['projected_overage_amount'], 2) }}
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                    Accrued to date: {{ $currentMerchant->currency }} {{ number_format($metrics['projected_overage_revenue']['accrued_overage_amount'], 2) }}
                </div>
            </div>

            <!-- KPI 3: Active Plan -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px;">Active Plan</div>
                <div style="font-size: 20px; font-weight: 700; color: #6ee7b7;">
                    {{ $metrics['active_plan'] }}
                </div>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                    Cycle: 30 days metered billing
                </div>
            </div>
        </div>

        <!-- Wireframe Two-Column Layout -->
        <div class="wireframe-grid">
            <!-- Left Column: Top 5 Customers Table & Daily Usage Trend Chart -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Top 5 Customers by Usage (this cycle) -->
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #fff;">Top 5 Customers by Usage (this cycle)</h4>
                        <span style="font-size: 11px; color: var(--text-muted);">(top 5, sorted desc)</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th style="text-align: right;">Usage</th>
                                <th style="text-align: right;">% of Allowance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['top_customers'] as $cust)
                                <tr>
                                    <td>
                                        <strong>{{ $cust['name'] }}</strong>
                                        <div style="font-size: 10px; color: var(--text-muted);">{{ $cust['email'] }}</div>
                                    </td>
                                    <td style="text-align: right; font-family: var(--font-mono); font-weight: 700;">
                                        {{ number_format($cust['total_usage_units']) }}
                                    </td>
                                    <td style="text-align: right; font-family: var(--font-mono); color: {{ $cust['percentage_of_allowance'] > 100 ? '#f87171' : '#6ee7b7' }};">
                                        {{ $cust['percentage_of_allowance'] }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted);">No usage recorded this cycle.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Daily Usage Trend (last 30 days) SVG Sparkline -->
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #fff;">Daily Usage Trend (last 30 days)</h4>
                        <span style="font-size: 11px; color: var(--text-muted);">usage / day</span>
                    </div>
                    @php
                        $trend = $metrics['daily_usage_trend'];
                        $maxUnits = max(1, max(array_column($trend, 'units') ?: [1]));
                        $svgWidth = 600;
                        $svgHeight = 120;
                        $points = [];
                        $count = count($trend);
                        foreach ($trend as $i => $pt) {
                            $x = $count > 1 ? round(($i / ($count - 1)) * ($svgWidth - 20)) + 10 : 10;
                            $y = round($svgHeight - 15 - (($pt['units'] / $maxUnits) * ($svgHeight - 30)));
                            $points[] = "{$x},{$y}";
                        }
                        $pointsStr = implode(' ', $points);
                    @endphp
                    <div style="width: 100%; overflow-x: auto;">
                        <svg viewBox="0 0 600 130" style="width: 100%; height: 130px; display: block;">
                            <defs>
                                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.4"/>
                                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0.0"/>
                                </linearGradient>
                            </defs>
                            <line x1="10" y1="105" x2="590" y2="105" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                            <line x1="10" y1="60" x2="590" y2="60" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                            <line x1="10" y1="15" x2="590" y2="15" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>

                            @if(count($points) > 1)
                                <polygon points="10,105 {{ $pointsStr }} 590,105" fill="url(#chartGrad)" />
                                <polyline fill="none" stroke="#6366f1" stroke-width="2.5" points="{{ $pointsStr }}" stroke-linecap="round" stroke-linejoin="round"/>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Right Column: Churn Risk Panel & System Status Panel -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Churn Risk Box -->
                <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.4); border-radius: var(--radius-md); padding: 18px;">
                    <div style="font-size: 13px; font-weight: 700; color: #f87171; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <span>▲</span> Churn Risk (usage ↓ &gt;50% MoM)
                    </div>
                    @if(count($metrics['churn_risk_customers']) > 0)
                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                            @foreach($metrics['churn_risk_customers'] as $risk)
                                <li style="font-size: 12px; color: #fca5a5; display: flex; justify-content: space-between; align-items: center;">
                                    <span>• <strong>{{ $risk['name'] }}</strong></span>
                                    <span style="font-family: var(--font-mono); font-weight: 700;">{{ $risk['drop_percentage'] }}% drop</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div style="font-size: 12px; color: #6ee7b7;">✓ No accounts with &gt;50% drop</div>
                    @endif
                </div>

                <!-- System status (informational) Box -->
                <div style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.4); border-radius: var(--radius-md); padding: 18px;">
                    <div style="font-size: 13px; font-weight: 700; color: #a5b4fc; margin-bottom: 12px;">
                        System status (informational)
                    </div>
                    <div style="font-size: 12px; color: var(--text-secondary); line-height: 1.8;">
                        <div>• Plan pricing cache: <span style="color: #6ee7b7; font-weight: 600;">Redis / Array, TTL 60m</span></div>
                        <div>• Nightly aggregation job: <span style="color: #6ee7b7; font-weight: 600;">queued, chunked (100 rows/batch)</span></div>
                        <div>• Usage endpoint: <span style="color: #6ee7b7; font-weight: 600;">rate-limited 600 req/min</span></div>
                        <div>• Idempotency: <span style="color: #6ee7b7; font-weight: 600;">unique key deduplication</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs">
        <button class="tab-btn active" id="tab-btn-metering" onclick="showTab('metering')">
            <span>📊</span> Live Metering & Subscriptions
        </button>
        <button class="tab-btn" id="tab-btn-intelligence" onclick="showTab('intelligence')">
            <span>🎯</span> Executive Intelligence
        </button>
        <button class="tab-btn" id="tab-btn-simulator" onclick="showTab('simulator')">
            <span>⚡</span> High-Volume Usage Ingest
        </button>
        <button class="tab-btn" id="tab-btn-plans" onclick="showTab('plans')">
            <span>🏷️</span> Plans & Pricing
        </button>
        <button class="tab-btn" id="tab-btn-invoices" onclick="showTab('invoices')">
            <span>🧾</span> Invoices ({{ count($invoices) }})
        </button>
        <button class="tab-btn" id="tab-btn-runner" onclick="showTab('runner')">
            <span>⏱️</span> Cycle End Billing Runner
        </button>
    </div>

    <!-- TAB 1: Live Metering & Subscriptions -->
    <div id="tab-metering" class="tab-pane active">
        <div class="section-header">
            <div class="section-title">Customer Subscriptions & Live Usage Meter</div>
            <button class="btn btn-secondary btn-sm" onclick="location.reload()">🔄 Refresh Metrics</button>
        </div>

        <div class="subscriptions-grid">
            @forelse($subscriptions as $subData)
                @php
                    $sub = $subData['model'];
                    $b = $subData['billing'];
                    $isDue = $sub->isDueForBilling();
                    $pct = $b['effective_allowance'] > 0 
                        ? min(100, round(($b['actual_units'] / $b['effective_allowance']) * 100))
                        : 100;
                    $isOverage = $b['overage_units'] > 0;
                @endphp
                <div class="sub-card" id="subscription-card-{{ $sub->id }}">
                    <div>
                        <div class="sub-header">
                            <div>
                                <div class="sub-customer-name">{{ $sub->customer->name }}</div>
                                <div class="subtitle">{{ $sub->customer->email }}</div>
                            </div>
                            <span class="sub-plan-badge">{{ $sub->plan->name }}</span>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
                            @if($b['is_prorated'])
                                <span class="badge badge-prorated">⏳ Prorated {{ number_format($b['proration_ratio'] * 100, 1) }}% (Mid-Cycle)</span>
                            @else
                                <span class="badge badge-full">✓ Full Cycle</span>
                            @endif

                            @if($isDue)
                                <span class="badge badge-due">🚨 Cycle Ended (Due)</span>
                            @else
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: #cbd5e1;">Active Period</span>
                            @endif
                        </div>

                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">
                            <strong>Cycle:</strong> {{ $sub->current_cycle_start->format('M d') }} – {{ $sub->current_cycle_end->format('M d, Y') }}<br>
                            <strong>Subscribed:</strong> {{ $sub->starts_at->format('M d, Y') }}
                        </div>

                        <!-- Usage Progress Bar -->
                        <div class="meter-container">
                            <div class="meter-label-row">
                                <span>Usage Meter (API Calls)</span>
                                <span>
                                    <strong>{{ number_format($b['actual_units']) }}</strong> / {{ number_format($b['effective_allowance']) }} units
                                </span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill {{ $isOverage ? 'progress-overage' : 'progress-normal' }}" style="width: {{ $pct }}%"></div>
                            </div>
                            @if($isOverage)
                                <div style="font-size: 11px; color: #f87171; margin-top: 6px; font-weight: 600;">
                                    ⚠️ Overage: +{{ number_format($b['overage_units']) }} units beyond included quota!
                                </div>
                            @endif
                        </div>

                        <!-- Billing Breakdown Preview -->
                        <div class="billing-preview-box">
                            <div>
                                <div class="preview-item-label">Base Fee (Prorated)</div>
                                <div class="preview-item-val">{{ $b['currency'] }} {{ number_format($b['base_amount'], 2) }}</div>
                            </div>
                            <div>
                                <div class="preview-item-label">Overage Fee</div>
                                <div class="preview-item-val" style="color: {{ $isOverage ? '#f87171' : 'inherit' }}">
                                    {{ $b['currency'] }} {{ number_format($b['overage_amount'], 2) }}
                                </div>
                            </div>
                            <div class="preview-total">
                                <span>Current Estimated Total</span>
                                <span style="font-size: 16px;">{{ $b['currency'] }} {{ number_format($b['total_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="sub-actions">
                        <button class="btn btn-primary btn-sm" id="btn-bill-sub-{{ $sub->id }}" onclick="generateSubInvoice({{ $sub->id }})">
                            🧾 Generate Cycle Invoice
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="previewSubInvoice({{ $sub->id }})">
                            🔍 Inspect Details
                        </button>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; padding: 40px; text-align: center; color: var(--text-muted);">
                    No subscriptions registered for this merchant yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB: Executive Intelligence & Churn Risk -->
    <div id="tab-intelligence" class="tab-pane">
        <div class="section-header">
            <div>
                <div class="section-title">Revenue Forecast & Customer Churn Intelligence</div>
                <div class="subtitle">Real-time projections and customer retention alerts powered by pre-aggregated rollups.</div>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="location.reload()">🔄 Refresh Intelligence</button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Top 5 Customers Table -->
            <div class="table-container" style="padding: 20px;">
                <h3 style="font-family: var(--font-heading); font-size: 16px; margin-bottom: 14px; color: #6ee7b7; display: flex; justify-content: space-between;">
                    <span>🏆 Top 5 Customers by Usage (This Month)</span>
                    <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Requirement 5a</span>
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Customer</th>
                            <th>Plan</th>
                            <th style="text-align: right;">MTD Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metrics['top_customers'] as $idx => $tc)
                            <tr>
                                <td style="font-family: var(--font-mono); color: var(--accent-primary); font-weight: 700;">#{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $tc['name'] }}</strong><br>
                                    <span style="font-size: 11px; color: var(--text-muted);">{{ $tc['email'] }}</span>
                                </td>
                                <td><span class="sub-plan-badge" style="font-size: 11px;">{{ $tc['plan_name'] }}</span></td>
                                <td style="text-align: right; font-family: var(--font-mono); font-weight: 700; color: #fff;">
                                    {{ number_format($tc['total_usage_units']) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">No usage events recorded for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Churn Risk Table -->
            <div class="table-container" style="padding: 20px;">
                <h3 style="font-family: var(--font-heading); font-size: 16px; margin-bottom: 14px; color: #fca5a5; display: flex; justify-content: space-between;">
                    <span>⚠️ Churn Risk Alerts (&gt;50% MoM Usage Drop)</span>
                    <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Requirement 5c</span>
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th style="text-align: right;">Last Month</th>
                            <th style="text-align: right;">This Month</th>
                            <th style="text-align: right;">Drop %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metrics['churn_risk_customers'] as $cc)
                            <tr>
                                <td>
                                    <strong>{{ $cc['name'] }}</strong><br>
                                    <span style="font-size: 11px; color: var(--text-muted);">{{ $cc['email'] }}</span>
                                </td>
                                <td style="text-align: right; font-family: var(--font-mono); color: var(--text-secondary);">
                                    {{ number_format($cc['previous_month_usage']) }}
                                </td>
                                <td style="text-align: right; font-family: var(--font-mono); color: #f87171; font-weight: 700;">
                                    {{ number_format($cc['current_month_usage']) }}
                                </td>
                                <td style="text-align: right;">
                                    <span class="badge badge-due" style="font-family: var(--font-mono);">
                                        -{{ $cc['drop_percentage'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6ee7b7; padding: 24px;">
                                    ✓ All customer usage patterns remain stable (&lt;50% month-over-month variance).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Projected Overage Deep-Dive Card -->
        <div class="stat-card" style="margin-bottom: 24px;">
            <h3 style="font-family: var(--font-heading); font-size: 16px; margin-bottom: 14px; color: #818cf8;">
                📊 Cycle End Projected Overage Breakdown (Requirement 5b)
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="background: rgba(0,0,0,0.25); padding: 16px; border-radius: var(--radius-sm);">
                    <div style="font-size: 12px; color: var(--text-secondary);">Accrued Overage Revenue</div>
                    <div style="font-size: 20px; font-weight: 700; color: #fff; margin-top: 4px;">
                        {{ $currentMerchant->currency }} {{ number_format($metrics['projected_overage_revenue']['accrued_overage_amount'], 2) }}
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.25); padding: 16px; border-radius: var(--radius-sm);">
                    <div style="font-size: 12px; color: var(--text-secondary);">Projected Cycle-End Run Rate</div>
                    <div style="font-size: 20px; font-weight: 700; color: #818cf8; margin-top: 4px;">
                        {{ $currentMerchant->currency }} {{ number_format($metrics['projected_overage_revenue']['projected_overage_amount'], 2) }}
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.25); padding: 16px; border-radius: var(--radius-sm);">
                    <div style="font-size: 12px; color: var(--text-secondary);">Subscriptions in Overage</div>
                    <div style="font-size: 20px; font-weight: 700; color: #f59e0b; margin-top: 4px;">
                        {{ $metrics['projected_overage_revenue']['subscriptions_with_overage_count'] }} of {{ $metrics['projected_overage_revenue']['active_subscriptions_count'] }}
                    </div>
                </div>
                <div style="background: rgba(0,0,0,0.25); padding: 16px; border-radius: var(--radius-sm);">
                    <div style="font-size: 12px; color: var(--text-secondary);">API Endpoint Reference</div>
                    <div style="font-size: 12px; font-family: var(--font-mono); color: #6ee7b7; margin-top: 6px;">
                        GET /merchants/{{ $currentMerchant->id }}/dashboard
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Usage Ingestion Simulator -->
    <div id="tab-simulator" class="tab-pane">
        <div class="section-header">
            <div>
                <div class="section-title">High-Volume Usage Ingestion Engine</div>
                <div class="subtitle">Supports single idempotent calls & high-throughput batch stream ingestion with atomic daily rollups.</div>
            </div>
        </div>

        <div class="simulator-grid">
            <!-- Single Event Form -->
            <div class="sim-card">
                <h3 style="font-family: var(--font-heading); font-size: 18px; margin-bottom: 16px;">Record Single Event (Idempotent)</h3>
                <form id="single-usage-form" onsubmit="handleSingleIngest(event)">
                    <div class="form-group">
                        <label class="form-label" for="single-customer">Target Customer</label>
                        <select id="single-customer" class="form-control" required>
                            @foreach($currentMerchant->customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="single-units">Units (API Calls)</label>
                        <input type="number" id="single-units" class="form-control" value="150" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="single-idempotency">Idempotency Key (Prevents Duplication)</label>
                        <input type="text" id="single-idempotency" class="form-control" value="req_{{ rand(100000, 999999) }}">
                    </div>

                    <button type="submit" id="btn-submit-single-ingest" class="btn btn-primary" style="width: 100%;">
                        🚀 Ingest Single Event
                    </button>
                </form>

                <div id="single-benchmark-box" class="benchmark-box"></div>
            </div>

            <!-- Batch Stress Test Simulator -->
            <div class="sim-card">
                <h3 style="font-family: var(--font-heading); font-size: 18px; margin-bottom: 16px;">Batch Stream Ingestion (Stress Test)</h3>
                <form id="batch-usage-form" onsubmit="handleBatchIngest(event)">
                    <div class="form-group">
                        <label class="form-label" for="batch-customer">Target Customer</label>
                        <select id="batch-customer" class="form-control" required>
                            @foreach($currentMerchant->customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="batch-count">Batch Size (Events in 1 Payload)</label>
                        <select id="batch-count" class="form-control">
                            <option value="100">100 Events Payload</option>
                            <option value="500" selected>500 Events Payload (Recommended)</option>
                            <option value="1000">1,000 Events Payload</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="batch-units-per">Units Per Event (Avg)</label>
                        <input type="number" id="batch-units-per" class="form-control" value="50" min="1">
                    </div>

                    <button type="submit" id="btn-submit-batch-ingest" class="btn btn-primary" style="width: 100%;">
                        ⚡ Execute Batch Ingestion
                    </button>
                </form>

                <div id="batch-benchmark-box" class="benchmark-box"></div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Plans Matrix -->
    <div id="tab-plans" class="tab-pane">
        <div class="section-header">
            <div>
                <div class="section-title">Merchant Plans & Overage Specifications</div>
                <div class="subtitle" style="margin-top: 4px;">
                    Define and manage multi-tenant subscription tiers. Changes immediately invalidate cached lookups and propagate to the customer portal.
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="openCreatePlanModal()">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Define New Plan
            </button>
        </div>

        <div class="subscriptions-grid">
            @forelse($currentMerchant->plans as $p)
                <div class="sub-card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: {{ $p->is_active ? 'var(--border-color)' : 'rgba(239, 68, 68, 0.25)' }}; opacity: {{ $p->is_active ? '1' : '0.65' }};">
                    <div>
                        <div class="sub-header">
                            <div>
                                <div class="sub-customer-name" style="display: flex; align-items: center; gap: 8px;">
                                    {{ $p->name }}
                                    @if($p->is_active)
                                        <span class="badge badge-full" style="font-size: 10px; padding: 2px 8px;">Active</span>
                                    @else
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); font-size: 10px; padding: 2px 8px;">Archived</span>
                                    @endif
                                </div>
                                <div class="subtitle" style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
                                    <span>Code: <code>{{ $p->code }}</code></span>
                                    <span>&bull;</span>
                                    <span style="color: #a5b4fc; font-weight: 600;">{{ $p->subscriptions_count ?? 0 }} Subscribers</span>
                                </div>
                            </div>
                            <span class="sub-plan-badge" style="font-size: 14px; font-weight: 800;">
                                {{ $currentMerchant->currency }} {{ number_format($p->base_price, 2) }}
                                <span style="font-size: 11px; font-weight: normal; color: var(--text-muted);">/ {{ $p->cycle_days }}d</span>
                            </span>
                        </div>

                        <p style="font-size: 13px; color: var(--text-secondary); margin: 12px 0 16px; min-height: 38px;">
                            {{ $p->description ?? 'Standard recurring subscription plan with metered usage allowance.' }}
                        </p>

                        <div class="billing-preview-box">
                            <div>
                                <div class="preview-item-label">Billing Cycle</div>
                                <div class="preview-item-val">{{ $p->cycle_days }} Days ({{ ucfirst($p->billing_cycle) }})</div>
                            </div>
                            <div>
                                <div class="preview-item-label">Included Units</div>
                                <div class="preview-item-val" style="color: #6ee7b7;">{{ number_format($p->included_usage_units) }} units</div>
                            </div>
                            <div>
                                <div class="preview-item-label">Overage Rate</div>
                                <div class="preview-item-val" style="color: #fbbf24;">{{ $currentMerchant->currency }} {{ number_format($p->overage_rate_per_unit, 4) }} / unit</div>
                            </div>
                            <div>
                                <div class="preview-item-label">Prorate Mid-Cycle</div>
                                <div class="preview-item-val">{{ $p->prorate_allowance ? 'Yes (Pro-rated)' : 'Full Allowance' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="margin-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 10px; padding-top: 14px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="openEditPlanModal({{ json_encode($p) }})">
                            ✏️ Edit Plan
                        </button>
                        <form action="{{ route('merchants.plans.toggle', [$currentMerchant->id, $p->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $p->is_active ? 'btn-secondary' : 'btn-primary' }}" style="font-size: 11px;">
                                {{ $p->is_active ? 'Archive Plan' : 'Activate Plan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 48px 24px; background: var(--bg-card); border-radius: var(--radius-md); color: var(--text-muted);">
                    No plans currently defined for {{ $currentMerchant->name }}. Click "+ Define New Plan" above to create your first tier.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 4: Invoices -->
    <div id="tab-invoices" class="tab-pane">
        <div class="section-header">
            <div class="section-title">Invoices & Line-Item Settlements</div>
            <button class="btn btn-secondary btn-sm" onclick="location.reload()">🔄 Refresh Invoices</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer</th>
                        <th>Billing Period</th>
                        <th>Proration</th>
                        <th>Base Fee</th>
                        <th>Overage Fee</th>
                        <th>Total Billed</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td><strong style="font-family: var(--font-mono); color: #a5b4fc;">{{ $inv->invoice_number }}</strong></td>
                            <td>{{ $inv->customer->name }}</td>
                            <td>{{ $inv->period_start->format('Y-m-d') }} → {{ $inv->period_end->format('Y-m-d') }}</td>
                            <td>
                                @if($inv->proration_ratio < 1.0)
                                    <span class="badge badge-prorated">{{ number_format($inv->proration_ratio * 100, 1) }}%</span>
                                @else
                                    <span class="badge badge-full">100%</span>
                                @endif
                            </td>
                            <td>{{ $inv->currency }} {{ number_format($inv->base_amount, 2) }}</td>
                            <td>{{ $inv->currency }} {{ number_format($inv->overage_amount, 2) }}</td>
                            <td><strong style="font-size: 14px;">{{ $inv->currency }} {{ number_format($inv->total_amount, 2) }}</strong></td>
                            <td>
                                <span class="badge badge-full">{{ ucfirst($inv->status) }}</span>
                            </td>
                            <td>
                                <button class="btn btn-secondary btn-sm" id="btn-view-inv-{{ $inv->id }}" onclick="openInvoiceModal({{ json_encode($inv) }})">
                                    👁️ View Breakdown
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                No invoices generated yet. Click "Generate Cycle Invoice" or run the Automated Billing Cycle.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 5: Automated Billing Cycle Runner -->
    <div id="tab-runner" class="tab-pane">
        <div class="section-header">
            <div>
                <div class="section-title">Automated Cycle-End Invoicing Runner</div>
                <div class="subtitle">Evaluates all subscriptions whose cycle end has arrived, applies proration and overage billing, produces invoices, and advances cycle dates.</div>
            </div>
        </div>

        <div class="sim-card" style="max-width: 700px;">
            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
                In production, this process executes automatically via the scheduler command <code>php artisan billing:process-cycle</code>. You can trigger it on demand below to process all due subscriptions for <strong>{{ $currentMerchant->name }}</strong>.
            </p>

            <button id="btn-run-billing-cycle" class="btn btn-primary" onclick="runBillingCycle()">
                🚀 Process Cycle Billing Now
            </button>

            <div id="runner-result-box" class="benchmark-box" style="margin-top: 20px;"></div>
        </div>
    </div>

    @endif
</div>

<!-- Invoice Details Modal -->
<div id="invoice-modal" class="modal-overlay" onclick="closeInvoiceModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeInvoiceModal()">&times;</button>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h2 id="modal-inv-number" style="font-family: var(--font-heading); font-size: 22px; color: #fff;">Invoice</h2>
                <div id="modal-inv-date" class="subtitle">Issued on</div>
            </div>
            <span id="modal-inv-status" class="badge badge-full">ISSUED</span>
        </div>

        <div class="invoice-bill-to">
            <div>
                <div class="preview-item-label">Billed To</div>
                <div id="modal-cust-name" style="font-weight: 700; color: #fff;"></div>
                <div id="modal-cust-email" style="font-size: 12px; color: var(--text-secondary);"></div>
            </div>
            <div style="text-align: right;">
                <div class="preview-item-label">Billing Period</div>
                <div id="modal-inv-period" style="font-weight: 600; color: #fff; font-size: 13px;"></div>
                <div id="modal-inv-proration" style="font-size: 12px; color: #fcd34d;"></div>
            </div>
        </div>

        <h4 style="font-family: var(--font-heading); font-size: 16px; margin: 18px 0 10px;">Itemized Invoice Line Items</h4>
        <div class="table-container" style="margin-bottom: 20px;">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: right;">Rate</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody id="modal-line-items"></tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
            <div style="min-width: 260px; background: rgba(0,0,0,0.3); padding: 16px; border-radius: var(--radius-sm);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Base Fee:</span>
                    <span id="modal-total-base" style="font-weight: 600;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                    <span style="color: var(--text-muted);">Overage Fee:</span>
                    <span id="modal-total-overage" style="font-weight: 600;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 10px; font-size: 18px; font-weight: 800; color: #a5b4fc;">
                    <span>Total Due:</span>
                    <span id="modal-total-amount"></span>
                </div>
            </div>
        </div>
    </div>
</div>

@if($currentMerchant)
<!-- Create Plan Modal -->
<div id="create-plan-modal" class="modal-overlay" onclick="closeCreatePlanModal(event)">
    <div class="modal-content" style="max-width: 620px;" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeCreatePlanModal()">&times;</button>
        <div style="margin-bottom: 20px;">
            <h2 style="font-family: var(--font-heading); font-size: 20px; color: #fff; display: flex; align-items: center; gap: 8px;">
                <span>✨</span> Define New Plan for {{ $currentMerchant->name }}
            </h2>
            <div class="subtitle">Configure pricing, cycle length, included allowances, and overage rates.</div>
        </div>

        <form action="{{ route('merchants.plans.store', $currentMerchant->id) }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="new-plan-name">Plan Name *</label>
                    <input type="text" id="new-plan-name" name="name" class="form-control" placeholder="e.g. Enterprise Plus" required oninput="autoSlugCode(this.value, 'new-plan-code')">
                </div>
                <div class="form-group">
                    <label class="form-label" for="new-plan-code">Code / Identifier</label>
                    <input type="text" id="new-plan-code" name="code" class="form-control" placeholder="auto-generated slug">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="new-plan-price">Base Price ({{ $currentMerchant->currency }}) *</label>
                    <input type="number" step="0.01" min="0" id="new-plan-price" name="base_price" class="form-control" placeholder="99.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new-plan-cycle">Billing Cycle *</label>
                    <select id="new-plan-cycle" name="billing_cycle" class="form-control" onchange="syncCycleDays(this.value, 'new-plan-days')" required>
                        <option value="monthly" selected>Monthly (30 Days)</option>
                        <option value="annual">Annual (365 Days)</option>
                        <option value="weekly">Weekly (7 Days)</option>
                        <option value="daily">Daily (1 Day)</option>
                        <option value="custom">Custom Days</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="new-plan-days">Cycle Length (Days) *</label>
                    <input type="number" min="1" max="365" id="new-plan-days" name="cycle_days" class="form-control" value="30" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="new-plan-included">Included Usage Units *</label>
                    <input type="number" min="0" id="new-plan-included" name="included_usage_units" class="form-control" placeholder="e.g. 50000" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new-plan-overage">Overage Rate per Unit ({{ $currentMerchant->currency }}) *</label>
                <input type="number" step="0.0001" min="0" id="new-plan-overage" name="overage_rate_per_unit" class="form-control" placeholder="0.0050" required>
                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Applied for each unit consumed beyond the included allowance.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="new-plan-desc">Description</label>
                <textarea id="new-plan-desc" name="description" class="form-control" rows="2" placeholder="Summary of tier capabilities and targets..."></textarea>
            </div>

            <div style="background: rgba(0,0,0,0.25); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #fff; margin-bottom: 8px;">
                    <input type="checkbox" name="prorate_allowance" value="1" checked style="width: 16px; height: 16px; accent-color: var(--accent-primary);">
                    <span><strong>Prorate Allowance Mid-Cycle</strong> (Adjust allowance proportionally for partial cycle periods)</span>
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #fff;">
                    <input type="checkbox" name="is_active" value="1" checked style="width: 16px; height: 16px; accent-color: var(--accent-primary);">
                    <span><strong>Activate Plan Immediately</strong> (Visible in customer subscription choices)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeCreatePlanModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save & Activate Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Plan Modal -->
<div id="edit-plan-modal" class="modal-overlay" onclick="closeEditPlanModal(event)">
    <div class="modal-content" style="max-width: 620px;" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="closeEditPlanModal()">&times;</button>
        <div style="margin-bottom: 20px;">
            <h2 style="font-family: var(--font-heading); font-size: 20px; color: #fff; display: flex; align-items: center; gap: 8px;">
                <span>✏️</span> Edit Plan — <span id="edit-plan-title-name"></span>
            </h2>
            <div class="subtitle">Modify pricing, included allowances, and overage specifications.</div>
        </div>

        <form id="edit-plan-form" action="" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="edit-plan-name">Plan Name *</label>
                    <input type="text" id="edit-plan-name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Plan Code (Fixed)</label>
                    <input type="text" id="edit-plan-code" class="form-control" disabled style="opacity: 0.6;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="edit-plan-price">Base Price ({{ $currentMerchant->currency }}) *</label>
                    <input type="number" step="0.01" min="0" id="edit-plan-price" name="base_price" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-plan-cycle">Billing Cycle *</label>
                    <select id="edit-plan-cycle" name="billing_cycle" class="form-control" onchange="syncCycleDays(this.value, 'edit-plan-days')" required>
                        <option value="monthly">Monthly (30 Days)</option>
                        <option value="annual">Annual (365 Days)</option>
                        <option value="weekly">Weekly (7 Days)</option>
                        <option value="daily">Daily (1 Day)</option>
                        <option value="custom">Custom Days</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="edit-plan-days">Cycle Length (Days) *</label>
                    <input type="number" min="1" max="365" id="edit-plan-days" name="cycle_days" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-plan-included">Included Usage Units *</label>
                    <input type="number" min="0" id="edit-plan-included" name="included_usage_units" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-plan-overage">Overage Rate per Unit ({{ $currentMerchant->currency }}) *</label>
                <input type="number" step="0.0001" min="0" id="edit-plan-overage" name="overage_rate_per_unit" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="edit-plan-desc">Description</label>
                <textarea id="edit-plan-desc" name="description" class="form-control" rows="2"></textarea>
            </div>

            <div style="background: rgba(0,0,0,0.25); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #fff; margin-bottom: 8px;">
                    <input type="checkbox" id="edit-plan-prorate" name="prorate_allowance" value="1" style="width: 16px; height: 16px; accent-color: var(--accent-primary);">
                    <span><strong>Prorate Allowance Mid-Cycle</strong></span>
                </label>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #fff;">
                    <input type="checkbox" id="edit-plan-active" name="is_active" value="1" style="width: 16px; height: 16px; accent-color: var(--accent-primary);">
                    <span><strong>Plan Active Status</strong></span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditPlanModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Toast Notification -->
<div id="toast" class="toast-notification"></div>

<script>
    const currentMerchantId = {{ $currentMerchant ? $currentMerchant->id : 'null' }};

    function showTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

        const targetPane = document.getElementById('tab-' + tabName);
        const targetBtn = document.getElementById('tab-btn-' + tabName);
        if (targetPane) targetPane.classList.add('active');
        if (targetBtn) targetBtn.classList.add('active');
    }

    // Auto-select tab if present in URL
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab) {
            showTab(tab);
        }
    });

    function switchMerchant(merchantId) {
        window.location.href = '/?merchant_id=' + merchantId;
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // Modal helpers for Plan Management
    function openCreatePlanModal() {
        document.getElementById('create-plan-modal').style.display = 'flex';
    }

    function closeCreatePlanModal(e) {
        if (!e || e.target.id === 'create-plan-modal' || e.target.classList.contains('modal-close')) {
            document.getElementById('create-plan-modal').style.display = 'none';
        }
    }

    function openEditPlanModal(plan) {
        document.getElementById('edit-plan-title-name').textContent = plan.name;
        document.getElementById('edit-plan-form').action = `/merchants/${currentMerchantId}/plans/${plan.id}/update`;
        document.getElementById('edit-plan-name').value = plan.name;
        document.getElementById('edit-plan-code').value = plan.code;
        document.getElementById('edit-plan-price').value = parseFloat(plan.base_price).toFixed(2);
        document.getElementById('edit-plan-cycle').value = plan.billing_cycle || 'monthly';
        document.getElementById('edit-plan-days').value = plan.cycle_days || 30;
        document.getElementById('edit-plan-included').value = plan.included_usage_units;
        document.getElementById('edit-plan-overage').value = parseFloat(plan.overage_rate_per_unit).toFixed(4);
        document.getElementById('edit-plan-desc').value = plan.description || '';
        document.getElementById('edit-plan-prorate').checked = !!plan.prorate_allowance;
        document.getElementById('edit-plan-active').checked = !!plan.is_active;

        document.getElementById('edit-plan-modal').style.display = 'flex';
    }

    function closeEditPlanModal(e) {
        if (!e || e.target.id === 'edit-plan-modal' || e.target.classList.contains('modal-close')) {
            document.getElementById('edit-plan-modal').style.display = 'none';
        }
    }

    function autoSlugCode(name, targetId) {
        const target = document.getElementById(targetId);
        if (target && !target.dataset.touched) {
            target.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    }

    function syncCycleDays(cycleValue, targetInputId) {
        const target = document.getElementById(targetInputId);
        if (!target) return;
        switch (cycleValue) {
            case 'annual': target.value = 365; break;
            case 'weekly': target.value = 7; break;
            case 'daily': target.value = 1; break;
            default: target.value = 30; break;
        }
    }

    // Close modals on Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreatePlanModal();
            closeEditPlanModal();
            closeInvoiceModal();
        }
    });

    // Single Usage Ingest
    async function handleSingleIngest(e) {
        e.preventDefault();
        const customerId = document.getElementById('single-customer').value;
        const units = parseInt(document.getElementById('single-units').value);
        const idempotencyKey = document.getElementById('single-idempotency').value;
        const benchBox = document.getElementById('single-benchmark-box');

        const startTime = performance.now();
        try {
            const resp = await fetch(`/api/v1/merchants/${currentMerchantId}/usage/ingest`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    customer_id: customerId,
                    units: units,
                    metric: 'api_calls',
                    idempotency_key: idempotencyKey
                })
            });
            const data = await resp.json();
            const elapsed = Math.round(performance.now() - startTime);

            benchBox.style.display = 'block';
            benchBox.innerHTML = `✓ Result: <strong>${data.status}</strong> | Latency: <strong>${elapsed}ms</strong><br>` +
                `Event ID: ${data.event_id || 'N/A'} | Units: +${units}`;

            showToast(`Ingested ${units} units (${data.status}) in ${elapsed}ms!`);
            // Regenerate idempotency key
            document.getElementById('single-idempotency').value = 'req_' + Math.floor(Math.random() * 900000 + 100000);
        } catch (err) {
            showToast('Ingest error: ' + err.message);
        }
    }

    // High Volume Batch Ingest
    async function handleBatchIngest(e) {
        e.preventDefault();
        const customerId = parseInt(document.getElementById('batch-customer').value);
        const count = parseInt(document.getElementById('batch-count').value);
        const avgUnits = parseInt(document.getElementById('batch-units-per').value);
        const benchBox = document.getElementById('batch-benchmark-box');

        benchBox.style.display = 'block';
        benchBox.textContent = `Generating and streaming ${count} events...`;

        const events = [];
        for (let i = 0; i < count; i++) {
            events.push({
                customer_id: customerId,
                units: Math.max(1, avgUnits + Math.floor(Math.random() * 20 - 10)),
                metric: 'api_calls',
                idempotency_key: 'batch_' + Math.random().toString(36).substring(2, 12) + '_' + Date.now()
            });
        }

        const startTime = performance.now();
        try {
            const resp = await fetch(`/api/v1/merchants/${currentMerchantId}/usage/batch`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ events: events })
            });
            const data = await resp.json();
            const elapsed = Math.round(performance.now() - startTime);

            benchBox.innerHTML = `⚡ <strong>Batch Ingest Benchmark:</strong><br>` +
                `• Total Ingested: <strong>${data.summary.processed} events</strong> (${data.summary.units_ingested.toLocaleString()} units)<br>` +
                `• Execution Time: <strong>${elapsed} ms</strong><br>` +
                `• Atomic Rollups: Successfully updated daily rollup table`;

            showToast(`Ingested batch of ${count} events in ${elapsed}ms!`);
        } catch (err) {
            showToast('Batch error: ' + err.message);
        }
    }

    // Generate Invoice for Subscription
    async function generateSubInvoice(subscriptionId) {
        if (!confirm('Generate cycle-end invoice for this subscription and roll to next cycle?')) return;

        try {
            const resp = await fetch(`/api/v1/merchants/${currentMerchantId}/subscriptions/${subscriptionId}/generate-invoice`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ advance_cycle: true })
            });
            const data = await resp.json();

            showToast('Invoice ' + data.data.invoice_number + ' generated successfully!');
            setTimeout(() => location.reload(), 1000);
        } catch (err) {
            showToast('Error: ' + err.message);
        }
    }

    // Preview Subscription
    async function previewSubInvoice(subscriptionId) {
        try {
            const resp = await fetch(`/api/v1/merchants/${currentMerchantId}/subscriptions/${subscriptionId}/preview-invoice`);
            const json = await resp.json();
            const b = json.data;

            alert(`Billing Calculation Preview:\n\n` +
                `Plan: ${b.plan_name}\n` +
                `Proration: ${(b.proration_ratio * 100).toFixed(1)}% (${b.is_prorated ? 'Mid-cycle' : 'Full cycle'})\n` +
                `Base Fee: ${b.currency} ${b.base_amount}\n` +
                `Usage: ${b.actual_units} units (Allowance: ${b.effective_allowance})\n` +
                `Overage: ${b.overage_units} units @ ${b.overage_rate} = ${b.currency} ${b.overage_amount}\n` +
                `Total Invoice: ${b.currency} ${b.total_amount}`
            );
        } catch (err) {
            showToast('Error: ' + err.message);
        }
    }

    // Cycle Billing Execution
    async function runBillingCycle() {
        const btn = document.getElementById('btn-run-billing-cycle');
        const resultBox = document.getElementById('runner-result-box');
        btn.disabled = true;
        btn.textContent = 'Processing Billing Cycle...';
        resultBox.style.display = 'block';
        resultBox.textContent = 'Querying due subscriptions, aggregating usage, calculating proration & overage...';

        try {
            const resp = await fetch(`/api/v1/merchants/${currentMerchantId}/billing/process-cycle`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
            });
            const data = await resp.json();

            resultBox.innerHTML = `✓ <strong>${data.message}</strong><br>` +
                `Processed: ${data.count} subscription(s). Reloading dashboard to view fresh invoices...`;

            showToast(data.message);
            setTimeout(() => location.reload(), 1500);
        } catch (err) {
            resultBox.textContent = 'Error: ' + err.message;
            btn.disabled = false;
            btn.textContent = '🚀 Process Cycle Billing Now';
        }
    }

    // Invoice Modal
    function openInvoiceModal(inv) {
        document.getElementById('modal-inv-number').textContent = inv.invoice_number;
        document.getElementById('modal-inv-date').textContent = 'Issued on ' + new Date(inv.issued_at).toLocaleDateString();
        document.getElementById('modal-cust-name').textContent = inv.customer ? inv.customer.name : 'Customer';
        document.getElementById('modal-cust-email').textContent = inv.customer ? inv.customer.email : '';
        document.getElementById('modal-inv-period').textContent = inv.period_start.substring(0, 10) + ' to ' + inv.period_end.substring(0, 10);
        document.getElementById('modal-inv-proration').textContent = inv.proration_ratio < 1.0 
            ? `⏳ ${(inv.proration_ratio * 100).toFixed(1)}% Prorated (Mid-Cycle)` 
            : '✓ Full Billing Cycle';

        document.getElementById('modal-total-base').textContent = `${inv.currency} ${parseFloat(inv.base_amount).toFixed(2)}`;
        document.getElementById('modal-total-overage').textContent = `${inv.currency} ${parseFloat(inv.overage_amount).toFixed(2)}`;
        document.getElementById('modal-total-amount').textContent = `${inv.currency} ${parseFloat(inv.total_amount).toFixed(2)}`;

        const tbody = document.getElementById('modal-line-items');
        tbody.innerHTML = '';

        if (inv.items && inv.items.length > 0) {
            inv.items.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <span class="badge ${item.type === 'overage' ? 'badge-prorated' : 'badge-full'}" style="margin-right: 6px;">${item.type}</span>
                        ${item.description}
                    </td>
                    <td style="text-align: right; font-family: var(--font-mono);">${parseInt(item.quantity).toLocaleString()}</td>
                    <td style="text-align: right; font-family: var(--font-mono);">${inv.currency} ${parseFloat(item.unit_price).toFixed(4)}</td>
                    <td style="text-align: right; font-weight: 700; font-family: var(--font-mono);">${inv.currency} ${parseFloat(item.amount).toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        document.getElementById('invoice-modal').style.display = 'flex';
    }

    function closeInvoiceModal(e) {
        if (!e || e.target.id === 'invoice-modal' || e.target.classList.contains('modal-close')) {
            document.getElementById('invoice-modal').style.display = 'none';
        }
    }
</script>

</body>
</html>

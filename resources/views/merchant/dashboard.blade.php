<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Merchant Portal Dashboard — Multi-Tenant SaaS Billing, Plan Management, and Metering Analytics.">
    <title>{{ $merchant->name }} — Merchant Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0d14;
            --bg-secondary: #111622;
            --bg-card: rgba(22, 28, 45, 0.75);
            --bg-card-hover: rgba(30, 38, 60, 0.9);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(99, 102, 241, 0.45);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --accent-glow: rgba(99, 102, 241, 0.28);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.25);
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            line-height: 1.6;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.12) 0%, transparent 42%),
                radial-gradient(circle at 90% 85%, rgba(139, 92, 246, 0.1) 0%, transparent 48%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 24px 80px;
        }

        /* Top Header */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 22px;
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
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 24px;
            color: #fff;
            box-shadow: 0 8px 22px var(--accent-glow);
        }

        .brand-info h1 {
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pill-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            font-family: var(--font-sans);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .brand-info .meta {
            font-size: 13px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 2px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Flash Alerts */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Top KPI Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-highlight);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .stat-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 30px;
            font-weight: 700;
            color: #fff;
        }

        .stat-sub {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 6px;
        }

        .progress-bar-bg {
            height: 6px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 4px;
            margin-top: 12px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(to right, #10b981, #6366f1);
            transition: width 0.5s ease;
        }

        /* Tabs Navigation */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 26px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            overflow-x: auto;
        }

        .tab-btn {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-secondary);
            font-family: var(--font-sans);
            font-size: 14px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .tab-btn.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(139, 92, 246, 0.18));
            border-color: var(--border-highlight);
        }

        .tab-counter {
            background: rgba(255, 255, 255, 0.12);
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 11px;
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

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 18px;
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
            opacity: 0.94;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.07);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .btn-danger-outline {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .btn-danger-outline:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #fff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Section Cards & Tables */
        .card-panel {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 28px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .panel-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Two-Column Analytics Layout */
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }

        @media (max-width: 1024px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Tables */
        .data-table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        table.data-table th {
            background: rgba(0, 0, 0, 0.35);
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
        }

        table.data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-secondary);
        }

        table.data-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-primary);
        }

        .table-primary-text {
            color: #fff;
            font-weight: 600;
        }

        .table-sub-text {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .badge-archived {
            background: rgba(148, 163, 184, 0.15);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        /* Plans Catalog Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .plan-card {
            background: rgba(17, 22, 34, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.25s;
        }

        .plan-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-highlight);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }

        .plan-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .plan-title {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .plan-price-row {
            margin: 14px 0 18px;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .plan-price {
            font-family: var(--font-heading);
            font-size: 32px;
            font-weight: 800;
            color: #fff;
        }

        .plan-price-period {
            font-size: 13px;
            color: var(--text-muted);
        }

        .plan-specs {
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 14px;
            margin-bottom: 18px;
            font-size: 13px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .spec-item {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
        }

        .spec-item strong {
            color: #fff;
            font-family: var(--font-mono);
        }

        /* Form Inputs */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: #fff;
            font-family: var(--font-sans);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            width: 100%;
            box-sizing: border-box;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px var(--accent-glow);
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            text-transform: none;
            letter-spacing: normal;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent-primary);
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: #111622;
            border: 1px solid var(--border-highlight);
            border-radius: var(--radius-lg);
            max-width: 680px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            transform: scale(0.95);
            transition: transform 0.25s ease;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 22px;
            cursor: pointer;
            line-height: 1;
        }

        .modal-close:hover {
            color: #fff;
        }

        /* Sparkline bar chart */
        .sparkline-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 100px;
            padding: 12px 0 4px;
        }

        .spark-bar {
            flex: 1;
            background: linear-gradient(to top, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.6));
            border-radius: 3px 3px 0 0;
            position: relative;
            transition: background 0.2s;
            cursor: pointer;
        }

        .spark-bar:hover {
            background: linear-gradient(to top, rgba(99, 102, 241, 0.5), rgba(139, 92, 246, 1));
        }

        .spark-bar:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 105%;
            left: 50%;
            transform: translateX(-50%);
            background: #1e2638;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            white-space: nowrap;
            border: 1px solid var(--border-color);
            z-index: 10;
        }
    </style>
</head>
<body>
    @php
        $currSymbol = $merchant->currency === 'EUR' ? '€' : '$';
    @endphp

    <div class="container">
        <!-- Top Header -->
        <header>
            <div class="brand-section">
                <div class="brand-logo">{{ substr($merchant->name, 0, 1) }}</div>
                <div class="brand-info">
                    <h1>
                        {{ $merchant->name }}
                        <span class="pill-badge">{{ $merchant->currency }}</span>
                        <span class="pill-badge">{{ $merchant->timezone }}</span>
                    </h1>
                    <div class="meta">
                        <span>Merchant ID: <strong>#{{ $merchant->id }}</strong></span>
                        <span>•</span>
                        <span>{{ $merchant->email }}</span>
                    </div>
                </div>
            </div>

            <div class="header-actions">
                <button class="btn btn-primary" onclick="openPlanModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Define New Plan
                </button>
                <button class="btn btn-secondary" onclick="openPasswordModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Change Password
                </button>
                <a href="{{ route('portal.login') }}" target="_blank" class="btn btn-secondary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Customer Portal
                </a>
                <a href="{{ route('dashboard') }}?merchant_id={{ $merchant->id }}" class="btn btn-secondary">
                    Engine Admin
                </a>
                <form action="{{ route('merchant.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger-outline">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#6ee7b7;cursor:pointer;font-weight:700;">×</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <div>
                    <strong>Action Required:</strong>
                    <ul style="margin-left: 20px; margin-top: 4px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#fca5a5;cursor:pointer;font-weight:700;">×</button>
            </div>
        @endif

        <!-- High-Level KPI Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">
                    <span>Current Cycle Usage</span>
                    <span class="pill-badge">{{ $metrics['active_plan'] }}</span>
                </div>
                <div class="stat-value">
                    {{ number_format($metrics['current_cycle_usage']['used_units']) }}
                </div>
                <div class="stat-sub">
                    of {{ number_format($metrics['current_cycle_usage']['total_allowance']) }} included units ({{ $metrics['current_cycle_usage']['percentage'] }}%)
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ min(100, $metrics['current_cycle_usage']['percentage']) }}%;"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">
                    <span>Projected Overage Revenue</span>
                    <span style="color: var(--warning); font-size: 11px; font-weight: 700;">RUN-RATE MODEL</span>
                </div>
                <div class="stat-value" style="color: #38bdf8;">
                    {{ $currSymbol }}{{ number_format($metrics['projected_overage_revenue']['projected_overage_amount'], 2) }}
                </div>
                <div class="stat-sub">
                    Accrued: {{ $currSymbol }}{{ number_format($metrics['projected_overage_revenue']['accrued_overage_amount'], 2) }} • {{ $metrics['projected_overage_revenue']['subscriptions_with_overage_count'] }} subs in overage
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">
                    <span>Active Plans Catalog</span>
                    <span class="badge badge-active">Cached & Synced</span>
                </div>
                <div class="stat-value">
                    {{ $merchant->plans->where('is_active', true)->count() }}
                </div>
                <div class="stat-sub">
                    {{ $merchant->plans->count() }} total registered plans for this tenant
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">
                    <span>Subscribed Customers</span>
                    <span class="pill-badge">{{ count($subscriptions) }} Active</span>
                </div>
                <div class="stat-value">
                    {{ count($subscriptions) }}
                </div>
                <div class="stat-sub">
                    Total Invoiced: {{ $currSymbol }}{{ number_format($totalBilled, 2) }}
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('analytics', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Overview & Analytics
            </button>
            <button class="tab-btn" onclick="switchTab('plans', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Plans Catalog
                <span class="tab-counter">{{ $merchant->plans->count() }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab('subscriptions', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Subscriptions & Metering
                <span class="tab-counter">{{ count($subscriptions) }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab('invoices', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Invoices History
                <span class="tab-counter">{{ $invoices->count() }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab('new-plan', this)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                + Define New Plan
            </button>
        </div>

        <!-- Tab 1: Overview & Analytics -->
        <div id="tab-analytics" class="tab-pane active">
            <div class="analytics-grid">
                <!-- Top 5 Customers by Usage (Req 5a) -->
                <div class="card-panel">
                    <div class="panel-header">
                        <div>
                            <div class="panel-title">
                                Top 5 Customers by Usage
                            </div>
                            <div class="panel-subtitle">Month-to-date metering breakdown and allowance consumption</div>
                        </div>
                        <span class="badge badge-active">Active Cycle</span>
                    </div>

                    @if(empty($metrics['top_customers']))
                        <p style="color: var(--text-muted); font-size: 13px; text-align: center; padding: 24px;">No usage events recorded for this month yet.</p>
                    @else
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Plan</th>
                                        <th>Units Consumed</th>
                                        <th>% Allowance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($metrics['top_customers'] as $cust)
                                        <tr>
                                            <td>
                                                <div class="table-primary-text">{{ $cust['name'] }}</div>
                                                <div class="table-sub-text">{{ $cust['email'] }}</div>
                                            </td>
                                            <td>
                                                <span class="pill-badge">{{ $cust['plan_name'] }}</span>
                                            </td>
                                            <td>
                                                <div class="table-primary-text" style="font-family: var(--font-mono);">
                                                    {{ number_format($cust['total_usage_units']) }}
                                                </div>
                                                <div class="table-sub-text">{{ number_format($cust['total_events']) }} events</div>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <div style="flex: 1; min-width: 60px;" class="progress-bar-bg">
                                                        <div class="progress-bar-fill" style="width: {{ min(100, $cust['percentage_of_allowance']) }}%; background: {{ $cust['percentage_of_allowance'] > 100 ? '#ef4444' : '#10b981' }};"></div>
                                                    </div>
                                                    <span style="font-size: 11px; font-weight: 700; color: {{ $cust['percentage_of_allowance'] > 100 ? '#f87171' : '#6ee7b7' }};">
                                                        {{ $cust['percentage_of_allowance'] }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- 30-Day Daily Usage Trend -->
                <div class="card-panel">
                    <div class="panel-header">
                        <div>
                            <div class="panel-title">
                                30-Day Ingestion Trend
                            </div>
                            <div class="panel-subtitle">Aggregated daily usage units ingested across all customers</div>
                        </div>
                        <span class="pill-badge">Last 30 Days</span>
                    </div>

                    @php
                        $maxUnits = max(1, collect($metrics['daily_usage_trend'])->max('units') ?? 1);
                    @endphp

                    <div class="sparkline-wrapper">
                        @foreach($metrics['daily_usage_trend'] as $day)
                            @php
                                $heightPct = max(4, round(($day['units'] / $maxUnits) * 100));
                            @endphp
                            <div class="spark-bar" style="height: {{ $heightPct }}%;" data-tooltip="{{ $day['day'] }}: {{ number_format($day['units']) }} units"></div>
                        @endforeach
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                        <span>30 days ago</span>
                        <span>Yesterday</span>
                        <span>Today</span>
                    </div>

                    <!-- Month-over-Month Churn Risk Alert (Req 5c) -->
                    <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <span style="font-size: 13px; font-weight: 700; color: #f59e0b; text-transform: uppercase; letter-spacing: 0.5px;">
                                ⚠️ Month-Over-Month Churn Risk (>50% Drop)
                            </span>
                            <span class="tab-counter" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">{{ count($metrics['churn_risk_customers']) }} detected</span>
                        </div>

                        @if(empty($metrics['churn_risk_customers']))
                            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-sm); padding: 12px 14px; font-size: 12px; color: #6ee7b7;">
                                ✓ Healthy retention: No customers have experienced a >50% month-over-month drop in usage.
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($metrics['churn_risk_customers'] as $risk)
                                    <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: var(--radius-sm); padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; font-size: 12px;">
                                        <div>
                                            <strong style="color: #fff;">{{ $risk['name'] }}</strong>
                                            <span style="color: var(--text-muted); font-size: 11px; margin-left: 6px;">({{ $risk['email'] }})</span>
                                            <div style="color: var(--text-secondary); margin-top: 2px;">
                                                Prev: {{ number_format($risk['previous_month_usage']) }} units → Curr: {{ number_format($risk['current_month_usage']) }} units
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span class="badge badge-danger">-{{ $risk['drop_percentage'] }}%</span>
                                            <div style="font-size: 10px; color: #fca5a5; margin-top: 2px; text-transform: uppercase;">{{ $risk['risk_level'] }} RISK</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Plans Catalog -->
        <div id="tab-plans" class="tab-pane">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Defined Plans for {{ $merchant->name }}</div>
                    <div class="panel-subtitle">Define, configure, and toggle plan tiers. Changes take immediate effect in the cache and Customer Portal.</div>
                </div>
                <button class="btn btn-primary" onclick="openPlanModal()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    Define New Plan
                </button>
            </div>

            <div class="plans-grid">
                @forelse($merchant->plans as $plan)
                    <div class="plan-card">
                        <div>
                            <div class="plan-header">
                                <div>
                                    <div class="plan-title">{{ $plan->name }}</div>
                                    <span class="pill-badge" style="font-family: var(--font-mono);">code: {{ $plan->code }}</span>
                                </div>
                                @if($plan->is_active)
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge badge-archived">Archived</span>
                                @endif
                            </div>

                            <div class="plan-price-row">
                                <span class="plan-price">{{ $currSymbol }}{{ number_format($plan->base_price, 2) }}</span>
                                <span class="plan-price-period">/ {{ $plan->billing_cycle }} ({{ $plan->cycle_days }} days)</span>
                            </div>

                            @if($plan->description)
                                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">{{ $plan->description }}</p>
                            @endif

                            <div class="plan-specs">
                                <div class="spec-item">
                                    <span>Included Units:</span>
                                    <strong>{{ number_format($plan->included_usage_units) }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>Overage Rate:</span>
                                    <strong>{{ $currSymbol }}{{ number_format($plan->overage_rate_per_unit, 4) }}/unit</strong>
                                </div>
                                <div class="spec-item">
                                    <span>Proration Support:</span>
                                    <strong style="color: {{ $plan->prorate_allowance ? '#34d399' : '#94a3b8' }};">
                                        {{ $plan->prorate_allowance ? 'Enabled (Instant)' : 'Disabled' }}
                                    </strong>
                                </div>
                                <div class="spec-item">
                                    <span>Active Subscribers:</span>
                                    <strong>{{ $plan->subscriptions_count ?? $plan->subscriptions()->count() }}</strong>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 14px;">
                            <form action="{{ route('merchant.plans.toggle', $plan) }}" method="POST" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn {{ $plan->is_active ? 'btn-secondary' : 'btn-primary' }}" style="width: 100%;">
                                    {{ $plan->is_active ? 'Archive Plan' : 'Activate Plan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 40px; text-align: center; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
                        <p style="color: var(--text-secondary); margin-bottom: 14px;">No plans have been defined yet for this merchant.</p>
                        <button class="btn btn-primary" onclick="openPlanModal()">Define Your First Plan</button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 3: Customer Subscriptions & Metering -->
        <div id="tab-subscriptions" class="tab-pane">
            <div class="card-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Customer Subscriptions & Live Metering</div>
                        <div class="panel-subtitle">Real-time usage meters, cycle dates, proration calculations, and accrued balances</div>
                    </div>
                    <span class="pill-badge">{{ count($subscriptions) }} Subscribed Customers</span>
                </div>

                @if(empty($subscriptions))
                    <p style="color: var(--text-muted); font-size: 13px; text-align: center; padding: 30px;">No active subscriptions for this merchant.</p>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Active Plan</th>
                                    <th>Billing Cycle</th>
                                    <th>Units Used / Allowance</th>
                                    <th>Accrued Total</th>
                                    <th>Proration Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subData)
                                    @php
                                        $sub = $subData['model'];
                                        $b = $subData['billing'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="table-primary-text">{{ $sub->customer->name }}</div>
                                            <div class="table-sub-text">{{ $sub->customer->email }}</div>
                                        </td>
                                        <td>
                                            <div class="table-primary-text">{{ $sub->plan->name }}</div>
                                            <div class="table-sub-text">{{ $currSymbol }}{{ number_format($sub->plan->base_price, 2) }}/{{ $sub->plan->billing_cycle }}</div>
                                        </td>
                                        <td>
                                            <div style="font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary);">
                                                {{ $sub->current_cycle_start->format('M d') }} - {{ $sub->current_cycle_end->format('M d, Y') }}
                                            </div>
                                            <div class="table-sub-text">Days remaining: {{ max(0, (int) now()->diffInDays($sub->current_cycle_end, false)) }}</div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; justify-content: space-between; font-family: var(--font-mono); font-size: 12px; margin-bottom: 4px;">
                                                <span>{{ number_format($b['actual_units']) }} / {{ number_format($b['effective_allowance']) }}</span>
                                                <span style="font-weight: 700; color: {{ $b['overage_units'] > 0 ? '#f87171' : '#6ee7b7' }};">
                                                    {{ $b['effective_allowance'] > 0 ? round(($b['actual_units'] / $b['effective_allowance']) * 100) : 100 }}%
                                                </span>
                                            </div>
                                            <div class="progress-bar-bg">
                                                <div class="progress-bar-fill" style="width: {{ min(100, $b['effective_allowance'] > 0 ? round(($b['actual_units'] / $b['effective_allowance']) * 100) : 100) }}%; background: {{ $b['overage_units'] > 0 ? '#ef4444' : '#10b981' }};"></div>
                                            </div>
                                            @if($b['overage_units'] > 0)
                                                <div class="table-sub-text" style="color: #f87171; margin-top: 4px;">
                                                    +{{ number_format($b['overage_units']) }} overage units ({{ $currSymbol }}{{ number_format($b['overage_amount'], 2) }})
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-primary-text" style="color: #38bdf8; font-family: var(--font-mono); font-size: 14px;">
                                                {{ $currSymbol }}{{ number_format($b['total_amount'], 2) }}
                                            </div>
                                            <div class="table-sub-text">
                                                Base: {{ $currSymbol }}{{ number_format($b['base_amount'] ?? $b['plan_base_price'], 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($b['is_prorated'])
                                                <span class="badge badge-warning" title="Prorated for mid-cycle plan switch">Prorated</span>
                                                <div class="table-sub-text">{{ $b['cycle_days'] ?? $sub->plan->cycle_days }} days</div>
                                            @else
                                                <span class="badge badge-active">Full Cycle</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 4: Invoices History -->
        <div id="tab-invoices" class="tab-pane">
            <div class="card-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Invoices History</div>
                        <div class="panel-subtitle">Final cycle-end generated invoices with itemized base fees and overage charges</div>
                    </div>
                    <span class="pill-badge">{{ $invoices->count() }} Invoices</span>
                </div>

                @if($invoices->isEmpty())
                    <p style="color: var(--text-muted); font-size: 13px; text-align: center; padding: 30px;">No cycle-end invoices generated yet for this merchant.</p>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Issued At</th>
                                    <th>Base Fee</th>
                                    <th>Overage Units</th>
                                    <th>Overage Fee</th>
                                    <th>Total Billed</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $inv)
                                    <tr>
                                        <td>
                                            <div class="table-primary-text" style="font-family: var(--font-mono);">
                                                {{ $inv->invoice_number }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-primary-text">{{ $inv->customer->name }}</div>
                                            <div class="table-sub-text">{{ $inv->customer->email }}</div>
                                        </td>
                                        <td>
                                            <div style="font-family: var(--font-mono); font-size: 12px;">
                                                {{ $inv->issued_at ? $inv->issued_at->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-family: var(--font-mono);">
                                                {{ $currSymbol }}{{ number_format($inv->base_amount, 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-family: var(--font-mono);">
                                                {{ number_format($inv->overage_units) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-family: var(--font-mono); color: {{ $inv->overage_amount > 0 ? '#fbbf24' : 'inherit' }};">
                                                {{ $currSymbol }}{{ number_format($inv->overage_amount, 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-primary-text" style="color: #38bdf8; font-family: var(--font-mono); font-size: 14px;">
                                                {{ $currSymbol }}{{ number_format($inv->total_amount, 2) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $inv->status === 'paid' ? 'badge-active' : 'badge-warning' }}">
                                                {{ ucfirst($inv->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab 5: Dedicated Form to Define New Plan -->
        <div id="tab-new-plan" class="tab-pane">
            <div class="card-panel" style="max-width: 800px; margin: 0 auto;">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Define New Subscription Plan</div>
                        <div class="panel-subtitle">Create a new pricing tier for {{ $merchant->name }}. Automatically published to cache and Customer Portal.</div>
                    </div>
                </div>

                <form action="{{ route('merchant.plans.store') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Plan Name *</label>
                            <input type="text" id="name" name="name" placeholder="e.g. Enterprise Plus, AI Scale, Developer Pro" required value="{{ old('name') }}">
                        </div>

                        <div class="form-group">
                            <label for="code">Unique Code / Slug (Optional)</label>
                            <input type="text" id="code" name="code" placeholder="e.g. enterprise-plus (auto-generated if empty)" value="{{ old('code') }}">
                        </div>

                        <div class="form-group">
                            <label for="base_price">Base Price ({{ $currSymbol }}) *</label>
                            <input type="number" id="base_price" name="base_price" step="0.01" min="0" placeholder="e.g. 99.00" required value="{{ old('base_price') }}">
                        </div>

                        <div class="form-group">
                            <label for="billing_cycle">Billing Cycle *</label>
                            <select id="billing_cycle" name="billing_cycle" required onchange="handleCycleChange(this.value)">
                                <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Monthly (30 Days)</option>
                                <option value="annual" {{ old('billing_cycle') === 'annual' ? 'selected' : '' }}>Annual (365 Days)</option>
                                <option value="weekly" {{ old('billing_cycle') === 'weekly' ? 'selected' : '' }}>Weekly (7 Days)</option>
                                <option value="daily" {{ old('billing_cycle') === 'daily' ? 'selected' : '' }}>Daily (1 Day)</option>
                                <option value="custom" {{ old('billing_cycle') === 'custom' ? 'selected' : '' }}>Custom Cycle Days</option>
                            </select>
                        </div>

                        <div class="form-group" id="cycleDaysGroup">
                            <label for="cycle_days">Cycle Duration (Days) *</label>
                            <input type="number" id="cycle_days" name="cycle_days" min="1" max="365" value="{{ old('cycle_days', 30) }}">
                        </div>

                        <div class="form-group">
                            <label for="included_usage_units">Included Usage Units *</label>
                            <input type="number" id="included_usage_units" name="included_usage_units" min="0" placeholder="e.g. 5000" required value="{{ old('included_usage_units') }}">
                        </div>

                        <div class="form-group">
                            <label for="overage_rate_per_unit">Overage Rate per Unit ({{ $currSymbol }}) *</label>
                            <input type="number" id="overage_rate_per_unit" name="overage_rate_per_unit" step="0.0001" min="0" placeholder="e.g. 0.05" required value="{{ old('overage_rate_per_unit') }}">
                        </div>

                        <div class="form-group full-width">
                            <label for="description">Plan Description / Value Proposition</label>
                            <textarea id="description" name="description" rows="3" placeholder="Explain what features and allowances are bundled into this tier...">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group full-width" style="display: flex; flex-direction: column; gap: 10px;">
                            <label class="checkbox-label">
                                <input type="checkbox" name="prorate_allowance" value="1" {{ old('prorate_allowance', '1') ? 'checked' : '' }}>
                                <span><strong>Prorate allowance:</strong> Scale included usage units proportionately when customers upgrade/downgrade mid-cycle.</span>
                            </label>

                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span><strong>Publish immediately:</strong> Make this plan instantly selectable in the Customer Portal.</span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Create & Publish Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Plan Creation (Quick-access anywhere) -->
    <div id="planModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: #fff;">Define New Plan</h2>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Create and publish a pricing tier for {{ $merchant->name }}</p>
                </div>
                <button class="modal-close" onclick="closePlanModal()">&times;</button>
            </div>

            <form action="{{ route('merchant.plans.store') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="m_name">Plan Name *</label>
                        <input type="text" id="m_name" name="name" placeholder="e.g. Enterprise Plus" required>
                    </div>

                    <div class="form-group">
                        <label for="m_code">Plan Code (Optional)</label>
                        <input type="text" id="m_code" name="code" placeholder="e.g. enterprise-plus">
                    </div>

                    <div class="form-group">
                        <label for="m_base_price">Base Price ({{ $currSymbol }}) *</label>
                        <input type="number" id="m_base_price" name="base_price" step="0.01" min="0" placeholder="e.g. 99.00" required>
                    </div>

                    <div class="form-group">
                        <label for="m_billing_cycle">Billing Cycle *</label>
                        <select id="m_billing_cycle" name="billing_cycle" required onchange="handleModalCycleChange(this.value)">
                            <option value="monthly">Monthly (30 Days)</option>
                            <option value="annual">Annual (365 Days)</option>
                            <option value="weekly">Weekly (7 Days)</option>
                            <option value="daily">Daily (1 Day)</option>
                            <option value="custom">Custom Cycle Days</option>
                        </select>
                    </div>

                    <div class="form-group" id="mCycleDaysGroup">
                        <label for="m_cycle_days">Cycle Duration (Days) *</label>
                        <input type="number" id="m_cycle_days" name="cycle_days" min="1" max="365" value="30">
                    </div>

                    <div class="form-group">
                        <label for="m_included_units">Included Usage Units *</label>
                        <input type="number" id="m_included_units" name="included_usage_units" min="0" placeholder="e.g. 5000" required>
                    </div>

                    <div class="form-group">
                        <label for="m_overage_rate">Overage Rate per Unit ({{ $currSymbol }}) *</label>
                        <input type="number" id="m_overage_rate" name="overage_rate_per_unit" step="0.0001" min="0" placeholder="e.g. 0.05" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="m_desc">Plan Description</label>
                        <textarea id="m_desc" name="description" rows="2" placeholder="Summary of what this plan includes..."></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label class="checkbox-label">
                            <input type="checkbox" name="prorate_allowance" value="1" checked>
                            <span><strong>Prorate allowance:</strong> Proportionate allowance scaling for mid-cycle changes.</span>
                        </label>
                    </div>

                    <div class="form-group full-width">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span><strong>Publish immediately:</strong> Make active for customer purchases.</span>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closePlanModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create & Publish Plan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Password Change -->
    <div id="passwordModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 500px;">
            <div class="modal-header">
                <div>
                    <h2 style="font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: #fff;">Change Account Password</h2>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">Update password for {{ $merchant->name }}</p>
                </div>
                <button class="modal-close" onclick="closePasswordModal()">&times;</button>
            </div>

            <form action="{{ route('merchant.password.update') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" placeholder="Enter current password (default: 123456)" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="password" minlength="6" placeholder="At least 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" placeholder="Repeat new password" required>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            const targetPane = document.getElementById('tab-' + tabId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
            if (btn) {
                btn.classList.add('active');
            }

            // Update URL query param without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.replaceState({}, '', url);
        }

        function openPlanModal() {
            document.getElementById('planModal').classList.add('active');
        }

        function closePlanModal() {
            document.getElementById('planModal').classList.remove('active');
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').classList.add('active');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('active');
        }

        function handleCycleChange(val) {
            const input = document.getElementById('cycle_days');
            if (val === 'annual') input.value = 365;
            else if (val === 'weekly') input.value = 7;
            else if (val === 'daily') input.value = 1;
            else if (val === 'monthly') input.value = 30;
        }

        function handleModalCycleChange(val) {
            const input = document.getElementById('m_cycle_days');
            if (val === 'annual') input.value = 365;
            else if (val === 'weekly') input.value = 7;
            else if (val === 'daily') input.value = 1;
            else if (val === 'monthly') input.value = 30;
        }

        // Close modal on background click or Escape key
        window.addEventListener('click', (e) => {
            const planModal = document.getElementById('planModal');
            if (e.target === planModal) {
                closePlanModal();
            }
            const pwdModal = document.getElementById('passwordModal');
            if (e.target === pwdModal) {
                closePasswordModal();
            }
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closePlanModal();
                closePasswordModal();
            }
        });

        // Initialize tab based on query param if present
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                const targetBtn = Array.from(document.querySelectorAll('.tab-btn')).find(b => 
                    b.getAttribute('onclick') && b.getAttribute('onclick').includes(`'${tab}'`)
                );
                if (targetBtn) {
                    switchTab(tab, targetBtn);
                }
            }
        });
    </script>
</body>
</html>

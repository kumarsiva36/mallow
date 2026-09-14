<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Customer Billing & Plan Management Portal. Real-time usage tracking, live proration, instant mid-cycle plan upgrades and downgrades.">
    <title>{{ $customer->name }} — Customer Billing Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0d14;
            --bg-secondary: #111622;
            --bg-card: rgba(22, 28, 45, 0.75);
            --bg-card-hover: rgba(30, 38, 60, 0.88);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(99, 102, 241, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --accent-glow: rgba(99, 102, 241, 0.3);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.3);
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
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 45%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 24px 80px;
        }

        /* Top Header */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
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
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        h1 {
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #fff;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 6px 14px;
            border-radius: 40px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
        }

        .user-info {
            line-height: 1.2;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        .user-merchant {
            font-size: 11px;
            color: var(--text-muted);
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
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-upgrade {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 4px 14px var(--success-glow);
        }
        .btn-upgrade:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-downgrade {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .btn-downgrade:hover {
            background: rgba(245, 158, 11, 0.25);
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

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.25);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-size: 13px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        /* Section Layouts */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Active Plan Hero Card */
        .hero-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-highlight);
            border-radius: var(--radius-lg);
            padding: 28px;
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary), #10b981);
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .plan-title-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .plan-name {
            font-family: var(--font-heading);
            font-size: 28px;
            font-weight: 800;
            color: #fff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-prorated {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .badge-segmented {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .cycle-dates {
            font-size: 13px;
            color: var(--text-secondary);
            font-family: var(--font-mono);
            background: rgba(0, 0, 0, 0.3);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
        }

        /* Metrics in Active Plan */
        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .metric-box {
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px 20px;
        }

        .metric-box .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 600;
        }

        .metric-box .val {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            font-family: var(--font-heading);
        }

        .metric-box .hint {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        /* Usage Progress Bar */
        .usage-container {
            margin-top: 20px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
        }

        .usage-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .usage-progress-track {
            width: 100%;
            height: 12px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .usage-progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .usage-legend {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Mid-Cycle Segments Accordion / List */
        .segments-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .segments-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #fbbf24;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .segment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
        }

        .segment-card {
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            font-size: 12px;
        }

        .segment-card-header {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            color: #fff;
            margin-bottom: 6px;
        }

        .segment-detail-row {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
            margin-top: 4px;
            font-family: var(--font-mono);
        }

        /* Plans Catalog Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .plan-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.25s;
        }

        .plan-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-highlight);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .plan-card.current {
            border: 2px solid var(--accent-primary);
            box-shadow: 0 10px 30px var(--accent-glow);
        }

        .plan-card.current::after {
            content: 'YOUR ACTIVE PLAN';
            position: absolute;
            top: -12px;
            right: 24px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 12px;
            letter-spacing: 0.8px;
        }

        .plan-card-header {
            margin-bottom: 20px;
        }

        .plan-name-card {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .plan-desc {
            font-size: 13px;
            color: var(--text-secondary);
            min-height: 40px;
        }

        .plan-price-box {
            margin: 20px 0;
            padding: 16px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .plan-price-val {
            font-family: var(--font-heading);
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .plan-price-period {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .plan-features {
            list-style: none;
            margin-bottom: 28px;
        }

        .plan-features li {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-features li svg {
            flex-shrink: 0;
            color: #10b981;
        }

        /* Invoices Table */
        .table-card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: 48px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(0, 0, 0, 0.4);
            padding: 14px 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-primary);
            font-size: 13px;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .status-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-paid {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-card {
            background: #111726;
            border: 1px solid var(--border-highlight);
            border-radius: var(--radius-lg);
            max-width: 540px;
            width: 100%;
            padding: 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            position: relative;
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
            line-height: 1;
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .proration-preview-box {
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 16px;
            margin: 16px 0;
        }

        .preview-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .preview-row.total {
            border-top: 1px solid var(--border-color);
            padding-top: 8px;
            margin-top: 8px;
            font-weight: 700;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }
        .empty-state svg {
            margin-bottom: 16px;
            opacity: 0.5;
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
                <h1>Customer Billing Portal</h1>
                <div class="subtitle">Real-time Metering, Usage Tracking & Self-Serve Plan Management</div>
            </div>
        </div>

        <div class="header-actions">
            <!-- Merchant Console Link -->
            <a href="{{ route('dashboard', ['merchant_id' => $merchant->id]) }}" class="btn btn-secondary btn-sm" title="Back to Merchant Management Console">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Merchant Console
            </a>

            <!-- Customer Profile -->
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ $customer->name }}</div>
                    <div class="user-merchant">{{ $merchant->name }}</div>
                </div>
            </div>

            <!-- Change Password Button -->
            <button class="btn btn-secondary btn-sm" onclick="openPasswordModal()" title="Change Account Password">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Change Password
            </button>

            <!-- Logout Form -->
            <form action="{{ route('portal.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" title="Sign out of customer portal">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 1. Active Subscription & Live Metering -->
    @if($activeSubscription && $billing)
        @php
            $usagePct = $billing['effective_allowance'] > 0 
                ? round(($billing['actual_units'] / $billing['effective_allowance']) * 100) 
                : 100;
            $barWidth = min(100, $usagePct);
            $barColor = $usagePct > 100 ? 'linear-gradient(90deg, #f59e0b, #ef4444)' : ($usagePct > 80 ? 'linear-gradient(90deg, #6366f1, #f59e0b)' : 'linear-gradient(90deg, #10b981, #059669)');
        @endphp
        <div class="hero-card">
            <div class="hero-top">
                <div>
                    <div class="plan-title-box">
                        <span class="plan-name">{{ $activeSubscription->plan->name }}</span>
                        <span class="badge badge-active">● Active</span>
                        @if($billing['is_segmented'])
                            <span class="badge badge-segmented">Mid-Cycle Changed</span>
                        @elseif($billing['is_prorated'])
                            <span class="badge badge-prorated">Prorated</span>
                        @endif
                    </div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                        Base Rate: <strong>{{ $merchant->currency }} {{ number_format($activeSubscription->plan->base_price, 2) }}</strong> / {{ $activeSubscription->plan->cycle_days }} days
                        &bull; Included Allowance: <strong>{{ number_format($billing['effective_allowance']) }} units</strong>
                    </div>
                </div>

                <div class="cycle-dates">
                    <span>Cycle: </span>
                    <strong>{{ $billing['period_start']->format('M d, Y') }}</strong>
                    <span> &rarr; </span>
                    <strong>{{ $billing['period_end']->format('M d, Y') }}</strong>
                    <span style="color: var(--accent-primary); margin-left: 8px;">
                        ({{ max(0, (int) now()->diffInDays($billing['period_end'], false)) }}d remaining)
                    </span>
                </div>
            </div>

            <!-- Metrics Row -->
            <div class="hero-metrics">
                <div class="metric-box">
                    <div class="label">Current Units Consumed</div>
                    <div class="val" style="color: {{ $usagePct > 100 ? '#f87171' : '#fff' }};">
                        {{ number_format($billing['actual_units']) }}
                    </div>
                    <div class="hint">of {{ number_format($billing['effective_allowance']) }} allowance</div>
                </div>

                <div class="metric-box">
                    <div class="label">Overage Units</div>
                    <div class="val" style="color: {{ $billing['overage_units'] > 0 ? '#fbbf24' : '#94a3b8' }};">
                        {{ number_format($billing['overage_units']) }}
                    </div>
                    <div class="hint">@ {{ $merchant->currency }} {{ number_format($activeSubscription->plan->overage_rate_per_unit, 4) }}/unit</div>
                </div>

                <div class="metric-box">
                    <div class="label">Base Plan Fee (Prorated)</div>
                    <div class="val">{{ $merchant->currency }} {{ number_format($billing['base_amount'], 2) }}</div>
                    <div class="hint">{{ $billing['is_prorated'] ? 'Adjusted for days active' : 'Full cycle fee' }}</div>
                </div>

                <div class="metric-box">
                    <div class="label">Projected Total Bill</div>
                    <div class="val" style="color: #34d399;">
                        {{ $merchant->currency }} {{ number_format($billing['total_amount'], 2) }}
                    </div>
                    <div class="hint">Base: {{ $merchant->currency }} {{ number_format($billing['base_amount'], 2) }} + Overage: {{ $merchant->currency }} {{ number_format($billing['overage_amount'], 2) }}</div>
                </div>
            </div>

            <!-- Usage Progress Bar -->
            <div class="usage-container">
                <div class="usage-label-row">
                    <div>
                        <strong>Live API Usage Meter:</strong>
                        <span style="color: var(--text-secondary); margin-left: 6px;">
                            {{ number_format($billing['actual_units']) }} / {{ number_format($billing['effective_allowance']) }} units
                        </span>
                    </div>
                    <div>
                        <strong style="color: {{ $usagePct > 100 ? '#f87171' : '#34d399' }};">
                            {{ $usagePct }}% Used
                        </strong>
                        @if($usagePct > 100)
                            <span style="color: #fbbf24; font-size: 11px; margin-left: 6px;">(+{{ number_format($billing['overage_units']) }} overage)</span>
                        @endif
                    </div>
                </div>

                <div class="usage-progress-track">
                    <div class="usage-progress-fill" style="width: {{ $barWidth }}%; background: {{ $barColor }};"></div>
                </div>

                <div class="usage-legend">
                    <span>0 units (Cycle Start)</span>
                    <span>Allowance Limit: {{ number_format($billing['effective_allowance']) }} units</span>
                    <span>{{ $usagePct > 100 ? 'Over limit' : 'Allowance Remaining: ' . number_format(max(0, $billing['effective_allowance'] - $billing['actual_units'])) }}</span>
                </div>
            </div>

            <!-- If Segments Exist (Mid-Cycle Changes) -->
            @if($billing['is_segmented'] && !empty($billing['segments']))
                <div class="segments-box">
                    <div class="segments-title">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Billing Cycle Plan Segments (Mid-Cycle History)
                    </div>
                    <div class="segment-grid">
                        @foreach($billing['segments'] as $segIndex => $seg)
                            <div class="segment-card">
                                <div class="segment-card-header">
                                    <span>Segment #{{ $segIndex + 1 }}: {{ $seg['plan_name'] }}</span>
                                    <span style="color: #fbbf24;">{{ round($seg['proration_ratio'] * 100, 1) }}% of cycle</span>
                                </div>
                                <div class="segment-detail-row">
                                    <span>Active Period:</span>
                                    <span>{{ $seg['period_start']->format('M d') }} &rarr; {{ $seg['period_end']->format('M d') }}</span>
                                </div>
                                <div class="segment-detail-row">
                                    <span>Prorated Base:</span>
                                    <span>{{ $merchant->currency }} {{ number_format($seg['base_amount'], 2) }}</span>
                                </div>
                                <div class="segment-detail-row">
                                    <span>Allowance:</span>
                                    <span>{{ number_format($seg['effective_allowance']) }} units</span>
                                </div>
                                <div class="segment-detail-row">
                                    <span>Units Used / Overage:</span>
                                    <span>{{ number_format($seg['actual_units']) }} / {{ number_format($seg['overage_units']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- No Active Subscription State -->
        <div class="hero-card" style="text-align: center; padding: 48px 24px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--accent-primary);">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 style="font-family: var(--font-heading); font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 8px;">No Active Subscription</h2>
            <p style="color: var(--text-secondary); max-width: 480px; margin: 0 auto 20px; font-size: 14px;">
                You do not currently have an active plan with {{ $merchant->name }}. Review our plans below and subscribe to get immediate API access and allowances.
            </p>
        </div>
    @endif

    <!-- 2. Available Plans Catalog (Purchase, Upgrade, Downgrade) -->
    <div class="section-header">
        <div>
            <div class="section-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Available Plans & Tiers
            </div>
            <div class="subtitle">Choose the perfect tier for your workload. Upgrade or downgrade instantly mid-cycle with prorated billing.</div>
        </div>
    </div>

    <div class="plans-grid">
        @foreach($plans as $plan)
            @php
                $isCurrent = $activeSubscription && $activeSubscription->plan_id === $plan->id;
                $isUpgrade = $activeSubscription && ((float)$plan->base_price > (float)$activeSubscription->plan->base_price);
                $isDowngrade = $activeSubscription && ((float)$plan->base_price < (float)$activeSubscription->plan->base_price);
            @endphp
            <div class="plan-card {{ $isCurrent ? 'current' : '' }}" id="plan-card-{{ $plan->id }}">
                <div>
                    <div class="plan-card-header">
                        <div class="plan-name-card">{{ $plan->name }}</div>
                        <div class="plan-desc">{{ $plan->description ?? 'Ideal for standard SaaS production workflows and API metering.' }}</div>
                    </div>

                    <div class="plan-price-box">
                        <div class="plan-price-val">
                            <span>{{ $merchant->currency }} {{ number_format($plan->base_price, 2) }}</span>
                            <span class="plan-price-period">/ {{ $plan->cycle_days }} days</span>
                        </div>
                    </div>

                    <ul class="plan-features">
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <strong>{{ number_format($plan->included_usage_units) }}</strong> included units / cycle
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $merchant->currency }} {{ number_format($plan->overage_rate_per_unit, 4) }} / overage unit
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $plan->prorate_allowance ? 'Dynamic mid-cycle prorated allowance' : 'Full allowance on activation' }}
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Idempotent 600 req/min high-volume usage API
                        </li>
                    </ul>
                </div>

                <!-- CTA Actions -->
                <div>
                    @if(!$activeSubscription)
                        <!-- Purchase Form -->
                        <form action="{{ route('portal.purchase') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                Subscribe to {{ $plan->name }}
                            </button>
                        </form>
                    @elseif($isCurrent)
                        <!-- Current Plan Disabled Button -->
                        <button type="button" class="btn btn-secondary" style="width: 100%; opacity: 0.6; cursor: default;" disabled>
                            ✓ Current Plan
                        </button>
                    @elseif($isUpgrade)
                        <!-- Upgrade Button -->
                        <button type="button" 
                                class="btn btn-upgrade" 
                                style="width: 100%;"
                                onclick="openPlanChangeModal({{ $activeSubscription->id }}, {{ $plan->id }}, '{{ addslashes($activeSubscription->plan->name) }}', '{{ addslashes($plan->name) }}', '{{ $merchant->currency }}', {{ $activeSubscription->plan->base_price }}, {{ $plan->base_price }}, 'upgrade')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            Upgrade to {{ $plan->name }}
                        </button>
                    @else
                        <!-- Downgrade Button -->
                        <button type="button" 
                                class="btn btn-downgrade" 
                                style="width: 100%;"
                                onclick="openPlanChangeModal({{ $activeSubscription->id }}, {{ $plan->id }}, '{{ addslashes($activeSubscription->plan->name) }}', '{{ addslashes($plan->name) }}', '{{ $merchant->currency }}', {{ $activeSubscription->plan->base_price }}, {{ $plan->base_price }}, 'downgrade')">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            Downgrade to {{ $plan->name }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- 3. Invoices History -->
    <div class="section-header">
        <div>
            <div class="section-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Invoices & Billing History
            </div>
            <div class="subtitle">Detailed breakdown of past billing cycles, base plan charges, and metered overage.</div>
        </div>
    </div>

    <div class="table-card">
        @if($invoices->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Cycle Period</th>
                        <th>Base Amount</th>
                        <th>Overage Amount</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        <tr>
                            <td>
                                <strong style="font-family: var(--font-mono); color: #fff;">{{ $inv->invoice_number }}</strong>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $inv->issued_at ? $inv->issued_at->format('M d, Y') : 'Draft' }}</div>
                            </td>
                            <td>
                                <span style="font-family: var(--font-mono); font-size: 12px;">
                                    {{ $inv->period_start->format('M d, Y') }} &rarr; {{ $inv->period_end->format('M d, Y') }}
                                </span>
                                @if((float)$inv->proration_ratio < 1.0)
                                    <div style="font-size: 11px; color: #a5b4fc;">Prorated ({{ round((float)$inv->proration_ratio * 100) }}%)</div>
                                @endif
                            </td>
                            <td>{{ $inv->currency }} {{ number_format($inv->base_amount, 2) }}</td>
                            <td>
                                @if((float)$inv->overage_amount > 0)
                                    <span style="color: #fbbf24; font-weight: 600;">+{{ $inv->currency }} {{ number_format($inv->overage_amount, 2) }}</span>
                                @else
                                    <span style="color: var(--text-muted);">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-family: var(--font-heading); font-size: 15px; color: #fff;">
                                    {{ $inv->currency }} {{ number_format($inv->total_amount, 2) }}
                                </strong>
                            </td>
                            <td>
                                <span class="status-badge {{ $inv->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="showInvoiceDetails({{ json_encode($inv) }}, {{ json_encode($inv->items) }})">
                                    View Breakdown
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <div>No past invoices generated yet for this customer.</div>
            </div>
        @endif
    </div>
</div>

<!-- Plan Change Confirmation Modal -->
<div id="plan-change-modal" class="modal-overlay">
    <div class="modal-card">
        <button class="modal-close" onclick="closePlanChangeModal()">&times;</button>
        <div class="modal-title" id="modal-title-text">
            Confirm Plan Change
        </div>

        <div class="modal-body">
            <p id="modal-desc-text" style="margin-bottom: 14px;"></p>

            <div class="proration-preview-box">
                <div class="preview-row">
                    <span style="color: var(--text-muted);">Current Plan:</span>
                    <strong id="modal-curr-plan" style="color: #fff;"></strong>
                </div>
                <div class="preview-row">
                    <span style="color: var(--text-muted);">New Selected Plan:</span>
                    <strong id="modal-new-plan" style="color: #34d399;"></strong>
                </div>
                <div class="preview-row">
                    <span style="color: var(--text-muted);">Monthly Pricing:</span>
                    <span id="modal-price-diff"></span>
                </div>
                <div class="preview-row total">
                    <span>Effective Timing:</span>
                    <span style="color: #60a5fa;">Immediate (Mid-Cycle Segmented)</span>
                </div>
            </div>

            <div style="background: rgba(99, 102, 241, 0.12); border-left: 3px solid var(--accent-primary); padding: 12px; border-radius: 4px; font-size: 12px; color: #cbd5e1; line-height: 1.5;">
                <strong>Proration Guarantee:</strong> Your billing cycle will be split into segments. You only pay for the exact days you used on your old tier and the remaining days on your new tier, with allowances and overage calculated accurately.
            </div>
        </div>

        <form id="plan-change-form" action="{{ route('portal.change-plan') }}" method="POST">
            @csrf
            <input type="hidden" name="subscription_id" id="modal-sub-id" value="">
            <input type="hidden" name="plan_id" id="modal-plan-id" value="">

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="closePlanChangeModal()">Cancel</button>
                <button type="submit" id="modal-submit-btn" class="btn btn-upgrade">
                    Confirm & Apply Change
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Invoice Breakdown Modal -->
<div id="invoice-breakdown-modal" class="modal-overlay">
    <div class="modal-card" style="max-width: 620px;">
        <button class="modal-close" onclick="closeInvoiceModal()">&times;</button>
        <div class="modal-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span id="inv-modal-number">Invoice Breakdown</span>
        </div>

        <div style="margin: 16px 0 20px; font-size: 13px; color: var(--text-secondary);" id="inv-modal-period"></div>

        <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 20px;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 10px 14px;">Item Description</th>
                        <th style="padding: 10px 14px; text-align: right;">Qty</th>
                        <th style="padding: 10px 14px; text-align: right;">Unit Price</th>
                        <th style="padding: 10px 14px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody id="inv-modal-items">
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 16px;">
            <span style="font-size: 13px; color: var(--text-muted);">Status: <strong id="inv-modal-status" style="color: #34d399;"></strong></span>
            <div style="font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #fff;">
                Total: <span id="inv-modal-total"></span>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 480px;">
        <button class="modal-close" onclick="closePasswordModal()">&times;</button>
        <h2 style="font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 6px;">Change Account Password</h2>
        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 24px;">Update password for {{ $customer->name }} ({{ $customer->email }})</p>

        <form action="{{ route('portal.password.update') }}" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary);">Current Password *</label>
                    <input type="password" name="current_password" style="background: rgba(0, 0, 0, 0.35); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 10px 14px; color: #fff; font-size: 14px; width: 100%; box-sizing: border-box; outline: none;" placeholder="Enter current password (default: 123456)" required>
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary);">New Password *</label>
                    <input type="password" name="password" minlength="6" style="background: rgba(0, 0, 0, 0.35); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 10px 14px; color: #fff; font-size: 14px; width: 100%; box-sizing: border-box; outline: none;" placeholder="At least 6 characters" required>
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary);">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" minlength="6" style="background: rgba(0, 0, 0, 0.35); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 10px 14px; color: #fff; font-size: 14px; width: 100%; box-sizing: border-box; outline: none;" placeholder="Repeat new password" required>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closePasswordModal()">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPlanChangeModal(subId, newPlanId, oldPlanName, newPlanName, currency, oldPrice, newPrice, type) {
        document.getElementById('modal-sub-id').value = subId;
        document.getElementById('modal-plan-id').value = newPlanId;
        document.getElementById('modal-curr-plan').textContent = oldPlanName + ' (' + currency + ' ' + Number(oldPrice).toFixed(2) + ')';
        document.getElementById('modal-new-plan').textContent = newPlanName + ' (' + currency + ' ' + Number(newPrice).toFixed(2) + ')';
        
        const isUpgrade = type === 'upgrade';
        const titleEl = document.getElementById('modal-title-text');
        const descEl = document.getElementById('modal-desc-text');
        const submitBtn = document.getElementById('modal-submit-btn');

        if (isUpgrade) {
            titleEl.innerHTML = '<span style="color: #34d399;">▲ Confirm Plan Upgrade</span>';
            descEl.textContent = 'You are upgrading your subscription to ' + newPlanName + '. Your new higher allowances and updated overage rate will apply immediately.';
            submitBtn.className = 'btn btn-upgrade';
            submitBtn.textContent = 'Confirm & Upgrade Now';
        } else {
            titleEl.innerHTML = '<span style="color: #fbbf24;">▼ Confirm Plan Downgrade</span>';
            descEl.textContent = 'You are switching to ' + newPlanName + '. Your cycle will be segmented, and your lower base price will take effect immediately.';
            submitBtn.className = 'btn btn-downgrade';
            submitBtn.textContent = 'Confirm & Downgrade Now';
        }

        const priceDiff = Math.abs(newPrice - oldPrice).toFixed(2);
        document.getElementById('modal-price-diff').textContent = (isUpgrade ? '+' : '-') + currency + ' ' + priceDiff + ' / cycle difference';

        document.getElementById('plan-change-modal').style.display = 'flex';
    }

    function closePlanChangeModal() {
        document.getElementById('plan-change-modal').style.display = 'none';
    }

    function showInvoiceDetails(invoice, items) {
        document.getElementById('inv-modal-number').textContent = invoice.invoice_number;
        
        const pStart = new Date(invoice.period_start).toLocaleDateString(undefined, {month: 'short', day: 'numeric', year: 'numeric'});
        const pEnd = new Date(invoice.period_end).toLocaleDateString(undefined, {month: 'short', day: 'numeric', year: 'numeric'});
        document.getElementById('inv-modal-period').textContent = 'Cycle Period: ' + pStart + ' — ' + pEnd;
        document.getElementById('inv-modal-status').textContent = invoice.status.toUpperCase();
        document.getElementById('inv-modal-total').textContent = invoice.currency + ' ' + Number(invoice.total_amount).toFixed(2);

        const tbody = document.getElementById('inv-modal-items');
        tbody.innerHTML = '';

        if (items && items.length > 0) {
            items.forEach(function(item) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="padding: 10px 14px; font-size: 12px; color: #fff;">${item.description}</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">${item.quantity}</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">${invoice.currency} ${Number(item.unit_price).toFixed(4)}</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; font-weight: 600; color: #fff;">${invoice.currency} ${Number(item.amount).toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 10px 14px; font-size: 12px; color: #fff;">Base Subscription Fee</td>
                <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">1</td>
                <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">${invoice.currency} ${Number(invoice.base_amount).toFixed(2)}</td>
                <td style="padding: 10px 14px; font-size: 12px; text-align: right; font-weight: 600; color: #fff;">${invoice.currency} ${Number(invoice.base_amount).toFixed(2)}</td>
            `;
            tbody.appendChild(tr);

            if (Number(invoice.overage_amount) > 0) {
                const trOver = document.createElement('tr');
                trOver.innerHTML = `
                    <td style="padding: 10px 14px; font-size: 12px; color: #fbbf24;">Usage Overage Charges</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">&mdash;</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; color: var(--text-secondary);">&mdash;</td>
                    <td style="padding: 10px 14px; font-size: 12px; text-align: right; font-weight: 600; color: #fbbf24;">${invoice.currency} ${Number(invoice.overage_amount).toFixed(2)}</td>
                `;
                tbody.appendChild(trOver);
            }
        }

        document.getElementById('invoice-breakdown-modal').style.display = 'flex';
    }

    function closeInvoiceModal() {
        document.getElementById('invoice-breakdown-modal').style.display = 'none';
    }

    function openPasswordModal() {
        document.getElementById('passwordModal').style.display = 'flex';
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
    }

    // Close modals on escape key or clicking backdrop
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePlanChangeModal();
            closeInvoiceModal();
            closePasswordModal();
        }
    });

    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closePlanChangeModal();
            closeInvoiceModal();
            closePasswordModal();
        }
    });
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Portal — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0d14;
            --bg-card: rgba(22, 28, 45, 0.78);
            --border-color: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(99, 102, 241, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --success: #10b981;
            --font-sans: 'Inter', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(139, 92, 246, 0.12) 0%, transparent 50%);
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            max-width: 600px;
            width: 100%;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(16px);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 26px;
            color: #fff;
            margin-bottom: 16px;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.35);
        }

        h1 {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-family: var(--font-sans);
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            background: rgba(0, 0, 0, 0.55);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }
        .divider span {
            padding: 0 14px;
        }

        .merchant-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .merchant-card {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
            cursor: pointer;
        }
        .merchant-card:hover {
            border-color: var(--border-highlight);
            background: rgba(99, 102, 241, 0.08);
            transform: translateY(-2px);
        }

        .merchant-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .merchant-avatar {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            color: #fff;
        }

        .merchant-name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 2px;
        }

        .merchant-meta {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            gap: 10px;
        }

        .badge-currency {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .btn-select {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border-color);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-select:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .footer-links {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            justify-content: center;
            gap: 16px;
        }
        .footer-links a {
            color: var(--accent-primary);
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-header">
        <div class="brand-icon">M</div>
        <h1>Merchant Management Portal</h1>
        <div class="subtitle">Multi-Tenant Plan Definition, Metering Oversight & Cycle Invoicing</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- 1-Click Quick Demo Login -->
    <div style="margin-bottom: 8px;">
        <label>1-Click Quick Login (Registered Tenants)</label>
    </div>

    <div class="merchant-grid">
        @foreach($merchants as $m)
            <form action="{{ route('merchant.login.post') }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="merchant_id" value="{{ $m->id }}">
                <input type="hidden" name="password" value="123456">
                <div class="merchant-card" onclick="this.closest('form').submit()">
                    <div class="merchant-info">
                        <div class="merchant-avatar">{{ strtoupper(substr($m->name, 0, 1)) }}</div>
                        <div>
                            <div class="merchant-name">{{ $m->name }}</div>
                            <div class="merchant-meta">
                                <span>{{ $m->email }}</span>
                                <span>&bull;</span>
                                <span class="badge-currency">{{ $m->currency }}</span>
                                <span>&bull;</span>
                                <span>{{ $m->plans_count ?? 0 }} Plans</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-select">Login &rarr;</button>
                </div>
            </form>
        @endforeach
    </div>

    <div class="divider">
        <span>Or Enter Credentials</span>
    </div>

    <!-- Direct Email & Password Login Form -->
    <form action="{{ route('merchant.login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Merchant Account Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. billing@nexuscloud.io" required>
        </div>
        <div class="form-group">
            <label for="password">Password (Default: 123456)</label>
            <div style="position: relative; display: flex; align-items: center;">
                <input type="password" id="password" name="password" value="123456" placeholder="Enter your password" required style="padding-right: 44px;">
                <button type="button" onclick="togglePasswordVisibility('password', this)" title="Show/Hide Password" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px; transition: color 0.2s;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>
        <button type="submit" class="btn-primary">Sign In to Merchant Portal</button>
    </form>

    <div class="footer-links">
        <a href="{{ route('portal.login') }}">Go to Customer Portal &rarr;</a>
        <span>&bull;</span>
        <a href="{{ route('dashboard') }}">System Admin Console</a>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
            btn.style.color = '#fff';
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
            btn.style.color = 'var(--text-muted)';
        }
    }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Portal — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">

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

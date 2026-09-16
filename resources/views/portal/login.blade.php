<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Billing Portal — Sign In & Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">

@php
    $currentTab = (old('name') || old('password_confirmation') || $errors->has('name') || $errors->has('password_confirmation') || ($initialTab ?? '') === 'register') ? 'register' : 'login';
@endphp

<div class="login-card">
    <div class="brand-header">
        <div class="brand-icon">M</div>
        <h1 id="card-title">{{ $currentTab === 'register' ? 'Customer Sign Up' : 'Customer Billing Portal' }}</h1>
        <div class="subtitle" id="card-subtitle">
            {{ $currentTab === 'register' ? 'Create a new customer account to subscribe and track usage' : 'Purchase, Upgrade or Downgrade Your SaaS Subscription' }}
        </div>
    </div>

    <!-- Segmented Auth Navigation Tabs -->
    <div class="auth-tabs" role="tablist">
        <button type="button" class="auth-nav-btn {{ $currentTab === 'login' ? 'active' : '' }}" id="tab-login-btn" onclick="switchAuthTab('login')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Sign In
        </button>
        <button type="button" class="auth-nav-btn {{ $currentTab === 'register' ? 'active' : '' }}" id="tab-register-btn" onclick="switchAuthTab('register')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Sign Up (New Customer)
        </button>
    </div>

    @if($errors->has('email') && str_contains($errors->first('email'), 'No customer account found'))
        <div class="alert alert-error" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first('email') }}</span>
            </div>
            <div style="margin-top: 4px; padding-top: 10px; border-top: 1px solid rgba(239, 68, 68, 0.35); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <span style="font-size: 13px; color: #fecaca; font-weight: 500;">Are you a new customer?</span>
                <button type="button" onclick="openSignupWithEmail('{{ addslashes(old('email', '')) }}')" style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Sign Up as New Customer {{ old('email') ? 'with ' . old('email') : '' }} &rarr;
                </button>
            </div>
        </div>
    @elseif($errors->any())
        <div class="alert alert-error">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                @if($errors->count() > 1)
                    <strong>Please review the following errors:</strong>
                    <ul style="margin-top: 4px; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @else
                    <div>{{ $errors->first() }}</div>
                @endif
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- ============================================== -->
    <!-- TAB PANE 1: SIGN IN                            -->
    <!-- ============================================== -->
    <div id="pane-login" style="{{ $currentTab === 'login' ? 'display: block;' : 'display: none;' }}">
        <form action="{{ route('portal.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Enter Customer Email</label>
                <input type="email" id="email" name="email" placeholder="e.g. devops@acme.corp" value="{{ old('email') }}" {{ $currentTab === 'login' ? 'autofocus' : '' }} required>
            </div>
            <div class="form-group">
                <label for="password">Password (Default: 123456)</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="password" name="password" value="123456" placeholder="Enter password" required style="padding-right: 44px;">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" title="Show/Hide Password" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px; transition: color 0.2s;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">Sign In with Email</button>
        </form>

        <!-- Prominent Sign Up Option Callout -->
        <div class="signup-callout">
            <div class="signup-callout-text">Don't have a customer account yet?</div>
            <button type="button" class="btn-signup-action" onclick="switchAuthTab('register')">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create New Customer Account (Sign Up) &rarr;
            </button>
        </div>

        <div class="divider">
            <span>or 1-click demo select</span>
        </div>

        <div class="quick-select-title">
            <span>Select a Seeded Customer Account</span>
            <span style="color: var(--accent-primary);">Instant Sign-In</span>
        </div>

        <div class="customer-tiles">
            @foreach($merchants as $m)
                @foreach($m->customers as $c)
                    @php
                        $plan = $c->activeSubscription?->plan;
                    @endphp
                    <form action="{{ route('portal.login.post') }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $c->id }}">
                        <input type="hidden" name="password" value="123456">
                        <button type="submit" class="customer-tile" style="width: 100%; border: none; text-align: left;">
                            <div>
                                <div class="tile-name">{{ $c->name }}</div>
                                <div class="tile-sub">{{ $c->email }} • {{ $m->name }}</div>
                            </div>
                            <span class="tile-plan-badge">{{ $plan ? $plan->name : 'No Active Plan' }}</span>
                        </button>
                    </form>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB PANE 2: SIGN UP (NEW CUSTOMER)             -->
    <!-- ============================================== -->
    <div id="pane-register" style="{{ $currentTab === 'register' ? 'display: block;' : 'display: none;' }}">
        <form action="{{ route('portal.register.post') }}" method="POST" id="registerForm">
            @csrf

            <!-- Merchant / Provider Selection -->
            <div class="form-group">
                <label for="reg_merchant_id">Select Service Provider (Tenant)<span class="required">*</span></label>
                <select id="reg_merchant_id" name="merchant_id" onchange="onMerchantChange(this.value)" required>
                    @foreach($merchants as $m)
                        <option value="{{ $m->id }}" 
                            {{ (old('merchant_id', $selectedMerchantId ?? '') == $m->id) ? 'selected' : '' }}
                            data-currency="{{ $m->currency }}">
                            {{ $m->name }} ({{ $m->plans->count() }} plans &bull; {{ $m->currency }})
                        </option>
                    @endforeach
                </select>
                <div class="input-hint">Your account will be created under this SaaS provider.</div>
            </div>

            <div class="form-grid">
                <!-- Customer Name -->
                <div class="form-group col-span-2">
                    <label for="reg_name">Full Name / Organization Name<span class="required">*</span></label>
                    <input type="text" id="reg_name" name="name" placeholder="e.g. Acme Tech or Alex Morgan" value="{{ old('name') }}" required>
                </div>

                <!-- Work Email Address -->
                <div class="form-group col-span-2">
                    <label for="reg_email">Work Email Address<span class="required">*</span></label>
                    <input type="email" id="reg_email" name="email" placeholder="e.g. alex@example.com" value="{{ old('email') }}" required>
                    <div class="input-hint">Used for portal sign-in and billing notifications.</div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="reg_password">Password (Min 6 chars)<span class="required">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input type="password" id="reg_password" name="password" placeholder="Create password" required style="padding-right: 44px;">
                        <button type="button" onclick="togglePasswordVisibility('reg_password', this)" title="Show/Hide" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Password Confirmation -->
                <div class="form-group">
                    <label for="reg_password_confirmation">Confirm Password<span class="required">*</span></label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <input type="password" id="reg_password_confirmation" name="password_confirmation" placeholder="Confirm password" required style="padding-right: 44px;">
                        <button type="button" onclick="togglePasswordVisibility('reg_password_confirmation', this)" title="Show/Hide" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Optional Initial Plan Selection -->
            <div class="form-group" style="margin-top: 4px;">
                <label>Choose Initial Plan (Optional)</label>
                <div class="plans-selector-container" id="plansContainer">
                    <!-- Free / No Plan Option -->
                    <label class="plan-radio-label {{ empty(old('plan_id', $selectedPlanId ?? '')) ? 'selected' : '' }}" onclick="selectPlanCard(this)">
                        <div class="plan-radio-left">
                            <input type="radio" name="plan_id" value="" {{ empty(old('plan_id', $selectedPlanId ?? '')) ? 'checked' : '' }}>
                            <div>
                                <div class="plan-info-title">I'll Choose a Plan Later</div>
                                <div class="plan-info-sub">Create account first, explore catalog & subscribe from dashboard</div>
                            </div>
                        </div>
                        <div class="plan-info-price" style="color: var(--text-secondary); font-size: 12px;">
                            $0.00 <span class="plan-info-period">now</span>
                        </div>
                    </label>

                    <!-- Dynamic Merchant Plans -->
                    @foreach($merchants as $m)
                        @foreach($m->plans as $p)
                            <label class="plan-radio-label plan-item-merchant-{{ $m->id }} {{ (old('plan_id', $selectedPlanId ?? '') == $p->id) ? 'selected' : '' }}" 
                                   data-merchant="{{ $m->id }}"
                                   style="{{ (old('merchant_id', $selectedMerchantId ?? '') == $m->id) ? '' : 'display:none;' }}"
                                   onclick="selectPlanCard(this)">
                                <div class="plan-radio-left">
                                    <input type="radio" name="plan_id" value="{{ $p->id }}" {{ (old('plan_id', $selectedPlanId ?? '') == $p->id) ? 'checked' : '' }}>
                                    <div>
                                        <div class="plan-info-title">{{ $p->name }}</div>
                                        <div class="plan-info-sub">Includes {{ number_format($p->included_usage_units) }} units &bull; +{{ $m->currency }} {{ number_format($p->overage_rate_per_unit, 4) }}/overage</div>
                                    </div>
                                </div>
                                <div class="plan-info-price">
                                    {{ $m->currency }} {{ number_format($p->base_price, 2) }}
                                    <div class="plan-info-period">/ {{ $p->cycle_days }}d</div>
                                </div>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 8px;">Complete Sign Up & Launch Portal &rarr;</button>
        </form>

        <div style="text-align: center; margin-top: 18px; font-size: 13px; color: var(--text-secondary);">
            Already have an account? 
            <button type="button" onclick="switchAuthTab('login')" style="background: none; border: none; color: #a5b4fc; font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 13px;">
                Sign in here &rarr;
            </button>
        </div>
    </div>

    <div class="back-link">
        <a href="{{ route('dashboard') }}">← Return to Merchant Admin Dashboard</a>
    </div>
</div>

<script>
    function switchAuthTab(tab) {
        const loginBtn = document.getElementById('tab-login-btn');
        const registerBtn = document.getElementById('tab-register-btn');
        const loginPane = document.getElementById('pane-login');
        const registerPane = document.getElementById('pane-register');
        const cardTitle = document.getElementById('card-title');
        const cardSubtitle = document.getElementById('card-subtitle');

        if (tab === 'register') {
            loginBtn.classList.remove('active');
            registerBtn.classList.add('active');
            loginPane.style.display = 'none';
            registerPane.style.display = 'block';
            cardTitle.textContent = 'Customer Sign Up';
            cardSubtitle.textContent = 'Create a new customer account to subscribe and track usage';
            window.history.replaceState(null, '', '#signup');
            const merchantSelect = document.getElementById('reg_merchant_id');
            if (merchantSelect) onMerchantChange(merchantSelect.value);
            const nameInput = document.getElementById('reg_name');
            if (nameInput) nameInput.focus();
        } else {
            registerBtn.classList.remove('active');
            loginBtn.classList.add('active');
            registerPane.style.display = 'none';
            loginPane.style.display = 'block';
            cardTitle.textContent = 'Customer Billing Portal';
            cardSubtitle.textContent = 'Purchase, Upgrade or Downgrade Your SaaS Subscription';
            window.history.replaceState(null, '', window.location.pathname);
            const emailInput = document.getElementById('email');
            if (emailInput) emailInput.focus();
        }
    }

    function openSignupWithEmail(email) {
        switchAuthTab('register');
        const regEmail = document.getElementById('reg_email');
        if (regEmail && email) {
            regEmail.value = email;
        }
        const regName = document.getElementById('reg_name');
        if (regName) {
            regName.focus();
        }
    }

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

    function selectPlanCard(card) {
        document.querySelectorAll('.plan-radio-label').forEach(el => el.classList.remove('selected'));
        card.classList.add('selected');
        const radio = card.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }

    function onMerchantChange(merchantId) {
        document.querySelectorAll('.plan-radio-label[data-merchant]').forEach(label => {
            const isMatch = label.getAttribute('data-merchant') === merchantId;
            label.style.display = isMatch ? 'flex' : 'none';
            if (!isMatch && label.classList.contains('selected')) {
                label.classList.remove('selected');
                const radio = label.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
                // Default back to "Choose later"
                const defaultOption = document.querySelector('.plan-radio-label:not([data-merchant])');
                if (defaultOption) {
                    defaultOption.classList.add('selected');
                    const defaultRadio = defaultOption.querySelector('input[type="radio"]');
                    if (defaultRadio) defaultRadio.checked = true;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#signup' || window.location.hash === '#register') {
            switchAuthTab('register');
        } else {
            const merchantSelect = document.getElementById('reg_merchant_id');
            if (merchantSelect) {
                onMerchantChange(merchantSelect.value);
            }
        }
    });
</script>
</body>
</html>

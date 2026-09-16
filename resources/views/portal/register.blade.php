<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Billing Portal — Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="auth-page">

<div class="register-card">
    <div class="brand-header">
        <div class="brand-icon">M</div>
        <h1>Customer Sign Up</h1>
        <div class="subtitle">Create your customer account to subscribe and track real-time usage</div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <strong>Please resolve the following:</strong>
                <ul style="margin-top: 4px; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('portal.register.post') }}" method="POST" id="registerForm">
        @csrf

        <!-- Merchant / Provider Selection -->
        <div class="form-group">
            <label for="merchant_id">Select Service Provider (Tenant)<span class="required">*</span></label>
            <select id="merchant_id" name="merchant_id" onchange="onMerchantChange(this.value)" required>
                @foreach($merchants as $m)
                    <option value="{{ $m->id }}" 
                        {{ (old('merchant_id', $selectedMerchantId) == $m->id) ? 'selected' : '' }}
                        data-currency="{{ $m->currency }}">
                        {{ $m->name }} ({{ $m->plans->count() }} active plans &bull; {{ $m->currency }})
                    </option>
                @endforeach
            </select>
            <div class="input-hint">Your account and subscription will be scoped to this provider.</div>
        </div>

        <div class="form-grid">
            <!-- Customer Name -->
            <div class="form-group col-span-2">
                <label for="name">Full Name / Organization Name<span class="required">*</span></label>
                <input type="text" id="name" name="name" placeholder="e.g. Acme Innovations or Sarah Connor" value="{{ old('name') }}" required autofocus>
            </div>

            <!-- Email Address -->
            <div class="form-group col-span-2">
                <label for="email">Work Email Address<span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="e.g. sarah@example.com" value="{{ old('email') }}" required>
                <div class="input-hint">Used for sign-in and billing invoice notifications.</div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password (Min 6 chars)<span class="required">*</span></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="password" name="password" placeholder="Create password" required style="padding-right: 44px;">
                    <button type="button" onclick="toggleVisibility('password', this)" title="Show/Hide" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password<span class="required">*</span></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required style="padding-right: 44px;">
                    <button type="button" onclick="toggleVisibility('password_confirmation', this)" title="Show/Hide" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; padding: 4px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Optional Initial Plan Selection -->
        <div class="form-group" style="margin-top: 6px;">
            <label>Choose Initial Plan (Optional)</label>
            <div class="plans-selector-container" id="plansContainer">
                <!-- Free / No Plan Option -->
                <label class="plan-radio-label {{ empty(old('plan_id', $selectedPlanId)) ? 'selected' : '' }}" onclick="selectPlanCard(this)">
                    <div class="plan-radio-left">
                        <input type="radio" name="plan_id" value="" {{ empty(old('plan_id', $selectedPlanId)) ? 'checked' : '' }}>
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
                        <label class="plan-radio-label plan-item-merchant-{{ $m->id }} {{ (old('plan_id', $selectedPlanId) == $p->id) ? 'selected' : '' }}" 
                               data-merchant="{{ $m->id }}"
                               style="{{ (old('merchant_id', $selectedMerchantId) == $m->id) ? '' : 'display:none;' }}"
                               onclick="selectPlanCard(this)">
                            <div class="plan-radio-left">
                                <input type="radio" name="plan_id" value="{{ $p->id }}" {{ (old('plan_id', $selectedPlanId) == $p->id) ? 'checked' : '' }}>
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

        <button type="submit" class="btn-primary">Create Customer Account &rarr;</button>
    </form>

    <div class="footer-links">
        <div>
            Already registered? 
            <a href="{{ route('portal.login') }}" class="highlight-link">Sign In to Customer Portal &rarr;</a>
        </div>
        <div>
            <a href="{{ route('dashboard') }}">← Return to Merchant Admin Dashboard</a>
        </div>
    </div>
</div>

<script>
    function toggleVisibility(inputId, btn) {
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const merchantSelect = document.getElementById('merchant_id');
        if (merchantSelect) {
            onMerchantChange(merchantSelect.value);
        }
    });
</script>
</body>
</html>

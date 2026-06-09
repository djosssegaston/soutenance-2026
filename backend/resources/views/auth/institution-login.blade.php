@extends('layouts.auth')

@push('meta')
<meta name="description" content="Alogoto – Connexion Institution">
<meta name="keywords" content="microfinance, connexion, institution, Alogoto">
@endpush

@push('title')
Connexion Institution
@endpush

@push('styles')
.phone-prefix { position: relative; display: flex; align-items: center; flex-shrink: 0; }
.phone-prefix-btn { display: flex; align-items: center; gap: 4px; padding: 12px 10px; border: none; border-right: 2px solid #e9ecef; background: transparent; cursor: pointer; font-size: 15px; font-weight: 600; color: #1a1a2e; white-space: nowrap; min-height: 48px; flex-shrink: 0; transition: background 0.2s; }
.phone-prefix-btn:hover { background: #f0f1f3; }
.phone-prefix-btn .fe-chevron-down { font-size: 12px; color: #adb5bd; transition: transform 0.2s; }
.phone-prefix-btn.open .fe-chevron-down { transform: rotate(180deg); }
.phone-prefix-dropdown { position: absolute; top: 100%; left: 0; z-index: 200; background: #fff; border: 2px solid #e9ecef; border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); max-height: 260px; overflow-y: auto; min-width: 230px; display: none; list-style: none; padding: 4px; margin: 4px 0 0; }
.phone-prefix-dropdown.show { display: block; }
.phone-prefix-dropdown li { display: flex; align-items: center; gap: 8px; padding: 10px 12px; cursor: pointer; border-radius: 6px; font-size: 14px; color: #1a1a2e; transition: background 0.15s; }
.phone-prefix-dropdown li:hover { background: #f0f1f3; }
.phone-prefix-dropdown li.active { background: #fff5e6; color: var(--login-accent); font-weight: 600; }
.phone-prefix-dropdown li .pays-nom { flex: 1; }
.phone-prefix-dropdown li .pays-code { color: #adb5bd; font-weight: 500; }
.phone-prefix-dropdown::-webkit-scrollbar { width: 4px; }
.phone-prefix-dropdown::-webkit-scrollbar-thumb { background: #e9ecef; border-radius: 4px; }
.input-group-custom:has(.phone-prefix-btn) input#mobile-num { padding-left: 4px; }
input#mobile-num:disabled { background: transparent; color: #adb5bd; cursor: default; }
@media (max-width: 576px) { .phone-prefix-dropdown { min-width: 200px; max-height: 200px; } .phone-prefix-btn { padding: 12px 6px; font-size: 13px; } }
@endpush

@section('content')
<div class="login-card">
    <div class="text-center">
        <a href="{{ url('/') }}" class="back-home-link"><i class="fe fe-arrow-left"></i> Retour à l'accueil</a>
    </div>

    <div class="login-logo">
        <a href="{{ url('/') }}">
            <img src="{{ url('frontend/asset/images/brand/logo-dark.png') }}" class="header-brand-img dark-logo" alt="Alogoto">
        </a>
    </div>

    <div class="login-title">
        <h2>Connexion Institution</h2>
        <p>Accédez à votre tableau de bord institutionnel</p>
    </div>

    @if (session('status'))
        <div class="alert-success">
            <i class="fe fe-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (request()->query('verified') == 1)
        <div class="alert-success">
            <i class="fe fe-check-circle"></i>
            <span>Email confirmé avec succès. Vous pouvez maintenant vous connecter.</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <i class="fe fe-alert-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.perform') }}" id="loginForm" novalidate>
        @csrf

        <div class="login-tabs" role="tablist">
            <button class="tab-btn active" id="tab-email-btn" data-tab="tab5" type="button">E-mail</button>
            <button class="tab-btn" id="tab-mobile-btn" data-tab="tab6" type="button">Mobile</button>
        </div>

        <div class="login-tab-content active" id="tab5">
            <div class="form-group">
                <label for="login-email">Adresse e-mail</label>
                <div class="input-group-custom @error('identifier') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-mail"></i></span>
                    <input type="email" id="login-email" name="identifier" class="form-control-custom" placeholder="exemple@institution.com" value="{{ old('identifier', $prefill_email ?? '') }}" autocomplete="email" required autofocus>
                </div>
                @error('identifier')
                    <div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="login-password">Mot de passe</label>
                <div class="input-group-custom @error('password') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-lock"></i></span>
                    <input type="password" id="login-password" name="password" class="form-control-custom" placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Afficher le mot de passe">
                        <i class="fe fe-eye-off"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember_me" id="remember-me" @checked(old('remember_me'))> Se souvenir de moi
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-login" id="login-submit">
                <span class="btn-text">Se connecter</span>
                <span class="spinner"></span>
            </button>
        </div>

        <div class="login-tab-content" id="tab6">
            <div class="form-group">
                <label for="mobile-num">Numéro de téléphone</label>
                <div class="input-group-custom">
                    <div class="phone-prefix">
                        <button type="button" class="phone-prefix-btn" id="phonePrefixBtn" title="Choisir le pays">
                            <span class="phone-code" id="phoneCode">+229</span>
                            <i class="fe fe-chevron-down"></i>
                        </button>
                        <ul class="phone-prefix-dropdown" id="phonePrefixDropdown" role="listbox" aria-label="Indicatif téléphonique"></ul>
                    </div>
                    <input type="tel" id="mobile-num" class="form-control-custom" placeholder="Sélectionnez d'abord l'indicatif" disabled autocomplete="tel">
                </div>
            </div>

            <div class="form-group">
                <label>Code de vérification (OTP)</label>
                <div class="otp-container" id="login-otp">
                    <input type="text" id="txt1" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" id="txt2" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" id="txt3" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    <input type="text" id="txt4" maxlength="1" inputmode="numeric" pattern="[0-9]">
                </div>
            </div>

            <p class="otp-note">Connectez-vous avec votre numéro mobile enregistré pour recevoir un code OTP.</p>

            <button type="button" class="btn-login" id="generate-otp">
                <span class="btn-text">Recevoir le code</span>
                <span class="spinner"></span>
            </button>
        </div>
    </form>

    <div class="social-login-section">
        <div class="divider">
            <span>Ou connectez-vous avec</span>
        </div>
        <div class="social-login-buttons">
            <a href="{{ route('social.redirect', ['provider' => 'twitter-oauth-2', 'role' => 'institution']) }}" class="social-btn" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
            <a href="{{ route('social.redirect', ['provider' => 'google', 'role' => 'institution']) }}" class="social-btn" aria-label="Google"><i class="fa fa-google"></i></a>
            <a href="{{ route('social.redirect', ['provider' => 'facebook', 'role' => 'institution']) }}" class="social-btn" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
        </div>
    </div>

    <div class="login-footer">
        <p>Vous n'avez pas de compte ? <a href="{{ route('register.institution') }}">Créer un compte</a></p>
    </div>
</div>
@endsection

@push('scripts')
@php use App\Models\Pay; $phoneCountries = Pay::where('actif', true)->get(['id', 'nom', 'code', 'indicatif'])->toArray(); @endphp
<script id="phoneCountriesData" type="application/json">@json($phoneCountries)</script>
<script>
    var otpSendUrl = '{{ route('otp.send') }}';
    var otpVerifyUrl = '{{ route('otp.verify') }}';
    var csrfToken = '{{ csrf_token() }}';

    // ===================== PHONE PREFIX SELECTOR =====================
    var phoneCountries = [];
    try { var scriptEl = document.getElementById('phoneCountriesData'); if (scriptEl) phoneCountries = JSON.parse(scriptEl.textContent); } catch(e) {}
    var EXTRA_PREFIXES = [
        { code: 'FR', nom: 'France', indicatif: '+33' },
        { code: 'BE', nom: 'Belgique', indicatif: '+32' },
        { code: 'CH', nom: 'Suisse', indicatif: '+41' },
        { code: 'CA', nom: 'Canada', indicatif: '+1' },
        { code: 'US', nom: 'États-Unis', indicatif: '+1' },
        { code: 'CM', nom: 'Cameroun', indicatif: '+237' },
    ];
    var existingCodes = {};
    phoneCountries.forEach(function(c) { existingCodes[c.code] = true; });
    EXTRA_PREFIXES.forEach(function(c) { if (!existingCodes[c.code]) phoneCountries.push(c); });
    phoneCountries.sort(function(a, b) {
        var aU = a.indicatif && a.indicatif.length <= 4 ? 0 : 1;
        var bU = b.indicatif && b.indicatif.length <= 4 ? 0 : 1;
        if (aU !== bU) return aU - bU;
        return (a.nom || '').localeCompare(b.nom || '');
    });

    var PHONE_RULES = {
        '+229': { min: 10, max: 10 }, '+226': { min: 8, max: 8 }, '+225': { min: 10, max: 10 },
        '+223': { min: 8, max: 8 }, '+227': { min: 8, max: 8 }, '+221': { min: 9, max: 9 },
        '+228': { min: 8, max: 8 }, '+33': { min: 9, max: 9 }, '+32': { min: 8, max: 9 },
        '+41': { min: 9, max: 9 }, '+1': { min: 10, max: 10 }, '+237': { min: 9, max: 9 },
    };

    var selectedPrefix = null;
    var phonePrefixBtn = document.getElementById('phonePrefixBtn');
    var phonePrefixDropdown = document.getElementById('phonePrefixDropdown');
    var phoneCode = document.getElementById('phoneCode');
    var phoneInput = document.getElementById('mobile-num');

    function buildPhoneDropdown() {
        phonePrefixDropdown.innerHTML = '';
        phoneCountries.forEach(function(c) {
            var li = document.createElement('li');
            li.dataset.code = c.code;
            li.dataset.indicatif = c.indicatif;
            li.setAttribute('role', 'option');
            li.innerHTML = '<span class="pays-nom">' + (c.nom || '') + '</span><span class="pays-code">' + (c.indicatif || '') + '</span>';
            li.addEventListener('click', function(e) {
                e.stopPropagation();
                selectPhonePrefix(c.indicatif);
                closePhoneDropdown();
            });
            phonePrefixDropdown.appendChild(li);
        });
    }

    function selectPhonePrefix(indicatif) {
        selectedPrefix = indicatif;
        phoneCode.textContent = indicatif;
        phoneInput.disabled = false;
        phoneInput.value = '';
        var rule = PHONE_RULES[indicatif];
        phoneInput.placeholder = rule ? (indicatif === '+229' ? '01 XX XX XX XX' : 'XX XX XX XX') : 'XX XX XX XX';
        phoneInput.focus();
    }

    function togglePhoneDropdown() {
        var isOpen = phonePrefixDropdown.classList.contains('show');
        if (isOpen) closePhoneDropdown(); else openPhoneDropdown();
    }

    function openPhoneDropdown() {
        phonePrefixDropdown.classList.add('show');
        phonePrefixBtn.classList.add('open');
        phonePrefixDropdown.querySelectorAll('li').forEach(function(li) {
            li.classList.toggle('active', li.dataset.indicatif === selectedPrefix);
        });
        document.addEventListener('click', closePhoneDropdownOutside);
    }

    function closePhoneDropdown() {
        phonePrefixDropdown.classList.remove('show');
        phonePrefixBtn.classList.remove('open');
        document.removeEventListener('click', closePhoneDropdownOutside);
    }

    function closePhoneDropdownOutside(e) {
        if (!phonePrefixBtn.contains(e.target) && !phonePrefixDropdown.contains(e.target)) closePhoneDropdown();
    }

    phonePrefixBtn.addEventListener('click', togglePhoneDropdown);

    phoneInput.addEventListener('input', function() {
        var raw = this.value.replace(/[^0-9]/g, '');
        var formatted = '';
        for (var i = 0; i < raw.length; i++) {
            if (i > 0 && i % 2 === 0) formatted += ' ';
            formatted += raw[i];
        }
        this.value = formatted;
    });

    function getFullPhone() {
        if (!selectedPrefix) return '';
        var digits = phoneInput.value.replace(/\s/g, '');
        return selectedPrefix + digits;
    }

    buildPhoneDropdown();
    selectPhonePrefix('+229');

    // ===================== TAB SWITCHING =====================
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
            document.querySelectorAll('.login-tab-content').forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        var pw = document.getElementById('login-password');
        var icon = this.querySelector('i');
        if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fe fe-eye'; }
        else { pw.type = 'password'; icon.className = 'fe fe-eye-off'; }
    });

    var otpInputs = document.querySelectorAll('#login-otp input');
    otpInputs.forEach(function(input, idx, arr) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length === 1 && idx < arr.length - 1) arr[idx + 1].focus();
            var fullCode = '';
            arr.forEach(function(inp) { fullCode += inp.value; });
            if (fullCode.length === 4) verifyOtp(fullCode);
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) arr[idx - 1].focus();
        });
        input.addEventListener('focus', function() { this.select(); });
    });

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        var btn = document.getElementById('login-submit');
        btn.classList.add('loading');
        btn.disabled = true;
    });

    document.getElementById('generate-otp').addEventListener('click', function() {
        var btn = this;
        var fullPhone = getFullPhone();
        if (!fullPhone || fullPhone.replace(/\D/g, '').length < 8) {
            showOtpStatus('Veuillez sélectionner l\'indicatif et entrer un numéro valide.', false);
            return;
        }
        btn.classList.add('loading');
        btn.disabled = true;
        fetch(otpSendUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ phone: fullPhone })
        })
        .then(function(r) { return r.json().catch(function() { return {}; }); })
        .then(function(data) {
            if (data.message) showOtpStatus(data.message, true);
            else showOtpStatus('Erreur lors de l\'envoi du code.', false);
        })
        .catch(function() { showOtpStatus('Erreur réseau.', false); })
        .finally(function() { btn.classList.remove('loading'); btn.disabled = false; });
    });

    function verifyOtp(code) {
        var fullPhone = getFullPhone();
        if (!fullPhone) { showOtpStatus('Numéro de téléphone manquant.', false); return; }
        fetch(otpVerifyUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ phone: fullPhone, code: code })
        })
        .then(function(r) { return r.json().catch(function() { return {}; }); })
        .then(function(data) {
            if (data.redirect) window.location.href = data.redirect;
            else {
                showOtpStatus(data.message || 'Code invalide.', false);
                otpInputs.forEach(function(inp) { inp.value = ''; });
                otpInputs[0].focus();
            }
        })
        .catch(function() { showOtpStatus('Erreur réseau.', false); });
    }

    function showOtpStatus(msg, success) {
        var note = document.querySelector('.otp-note');
        if (!note) return;
        note.textContent = msg;
        note.style.color = success ? '#16a34a' : '#dc3545';
        setTimeout(function() {
            note.style.color = '#6c757d';
            note.textContent = 'Connectez-vous avec votre numéro mobile enregistré pour recevoir un code OTP.';
        }, 6000);
    }
</script>
@endpush

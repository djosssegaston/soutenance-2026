@extends('layouts.auth')

@push('meta')
<meta name="description" content="Alogoto – Inscription Institution">
<meta name="keywords" content="microfinance, inscription, institution, Alogoto">
@endpush

@push('title')
Inscription Institution
@endpush

@push('styles')
:root {
    --step-transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.steps-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
    padding: 0 4px;
}

.step-indicator {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
}

.step-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    border: 2px solid #e9ecef;
    background: #fff;
    color: #adb5bd;
    transition: all var(--step-transition);
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.step-dot.active {
    border-color: var(--login-accent);
    background: var(--login-accent);
    color: #fff;
    box-shadow: 0 4px 12px rgba(252, 160, 40, 0.3);
}

.step-dot.completed {
    border-color: #10b981;
    background: #10b981;
    color: #fff;
}

.step-connector {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 4px;
    transition: background var(--step-transition);
    position: relative;
    z-index: 1;
}

.step-connector.completed {
    background: #10b981;
}

.step-label {
    display: none;
    font-size: 9px;
    color: #6c757d;
    text-align: center;
    margin-top: 6px;
    font-weight: 500;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
}

@media (min-width: 576px) {
    .step-label {
        display: block;
    }
    .step-dot {
        width: 40px;
        height: 40px;
        font-size: 15px;
    }
}

@media (min-width: 768px) {
    .step-label {
        font-size: 11px;
    }
}

.step-content {
    display: none;
    animation: stepFadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.step-content.active {
    display: block;
}

@keyframes stepFadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 4px;
}

.step-subtitle {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 24px;
}

.step-subtitle .required-hint {
    font-size: 11px;
    color: #adb5bd;
}

.step-subtitle .required-hint span {
    color: #dc3545;
}

.nav-buttons {
    display: flex;
    gap: 12px;
    margin-top: 28px;
}

.btn-prev {
    flex: 1;
    padding: 14px 24px;
    border: 2px solid #e9ecef;
    border-radius: var(--login-btn-radius);
    background: #fff;
    color: #495057;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-prev:hover {
    border-color: var(--login-accent);
    color: var(--login-accent);
}

.btn-prev:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.btn-next {
    flex: 1;
    padding: 14px 24px;
    border: none;
    border-radius: var(--login-btn-radius);
    background: linear-gradient(135deg, var(--login-accent), #FFB74D);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 24px rgba(252, 160, 40, 0.3);
    font-family: inherit;
}

.btn-next:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 32px rgba(252, 160, 40, 0.4);
}

.btn-next:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.btn-next.loading {
    pointer-events: none;
    position: relative;
    color: transparent;
}

.btn-next.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.form-row {
    display: flex;
    gap: 12px;
    margin-bottom: 0;
}

.form-row .form-group {
    flex: 1;
}

/* Password strength */
.password-strength {
    margin-top: 8px;
    display: none;
}

.password-strength.visible {
    display: block;
}

.password-strength .strength-bar {
    display: flex;
    gap: 4px;
    margin-bottom: 4px;
}

.password-strength .strength-bar span {
    flex: 1;
    height: 4px;
    border-radius: 2px;
    background: #e9ecef;
    transition: all 0.3s ease;
}

.password-strength .strength-bar span.active.weak { background: #ef4444; }
.password-strength .strength-bar span.active.medium { background: #f59e0b; }
.password-strength .strength-bar span.active.strong { background: #10b981; }
.password-strength .strength-bar span.active.very-strong { background: #059669; }

.password-strength .strength-text {
    font-size: 12px;
    color: #6c757d;
}

.password-strength .strength-text.weak { color: #ef4444; }
.password-strength .strength-text.medium { color: #f59e0b; }
.password-strength .strength-text.strong { color: #10b981; }
.password-strength .strength-text.very-strong { color: #059669; }

/* Upload zones */
.upload-zone {
    border: 2px dashed #e9ecef;
    border-radius: var(--login-input-radius);
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafbfc;
    position: relative;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.upload-zone:hover {
    border-color: var(--login-accent);
    background: var(--login-accent-soft);
}

.upload-zone.dragover {
    border-color: var(--login-accent);
    background: var(--login-accent-soft);
    box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
}

.upload-zone.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #f0fdf4;
}

.upload-zone .upload-icon {
    font-size: 32px;
    color: #adb5bd;
    margin-bottom: 8px;
    transition: color 0.3s ease;
}

.upload-zone:hover .upload-icon {
    color: var(--login-accent);
}

.upload-zone .upload-text {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
}

.upload-zone .upload-hint {
    font-size: 11px;
    color: #adb5bd;
    margin-top: 4px;
}

.upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-preview {
    display: none;
    margin-top: 12px;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    max-height: 160px;
    width: 100%;
}

.upload-preview.visible {
    display: block;
}

.upload-preview img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e9ecef;
}

.upload-preview .remove-file {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(0,0,0,0.6);
    color: #fff;
    border: none;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

.upload-preview .remove-file:hover {
    background: rgba(220, 38, 38, 0.8);
}

.upload-filename {
    font-size: 12px;
    color: #10b981;
    margin-top: 6px;
    text-align: center;
    font-weight: 500;
}

/* Radio cards */
.radio-cards {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.radio-card {
    flex: 1;
    min-width: 100px;
    position: relative;
}

.radio-card input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.radio-card label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 14px 10px;
    border: 2px solid #e9ecef;
    border-radius: var(--login-input-radius);
    background: #fafbfc;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    font-size: 13px;
    font-weight: 600;
    color: #495057;
}

.radio-card label i {
    font-size: 22px;
    color: #adb5bd;
    transition: color 0.3s ease;
}

.radio-card input:checked + label {
    border-color: var(--login-accent);
    background: var(--login-accent-soft);
    color: var(--login-accent);
    box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
}

.radio-card input:checked + label i {
    color: var(--login-accent);
}

.radio-card label:hover {
    border-color: var(--login-accent);
}

/* Radio options inline */
.radio-options-inline {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.radio-option-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e9ecef;
    border-radius: var(--login-input-radius);
    background: #fafbfc;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 600;
    color: #495057;
}

.radio-option-inline:hover {
    border-color: var(--login-accent);
}

.radio-option-inline input[type="radio"] {
    display: none;
}

.radio-option-inline input[type="radio"]:checked + .radio-custom-circle {
    border-color: var(--login-accent);
    background: var(--login-accent);
}

.radio-option-inline input[type="radio"]:checked + .radio-custom-circle::after {
    background: #fff;
}

.radio-option-inline:has(input:checked) {
    border-color: var(--login-accent);
    background: var(--login-accent-soft);
    color: var(--login-accent);
}

.radio-custom-circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.radio-custom-circle::after {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: transparent;
    transition: all 0.3s ease;
}

/* Location select */
select.location-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 10px;
    padding-right: 32px;
    min-width: 0;
    width: 100%;
}

select.location-select:disabled {
    background-color: #f0f1f3;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.7;
}

.input-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    min-width: 36px;
    height: 48px;
    color: var(--login-accent);
    font-size: 15px;
}

.required-star { color: #dc3545; margin-left: 2px; }

.input-group-custom textarea.form-control-custom {
    min-height: 48px;
    height: 48px;
    padding-top: 12px;
    padding-bottom: 12px;
    resize: none;
    line-height: 1.5;
    border: none;
    outline: none;
}

.input-group-custom textarea.form-control-custom::placeholder {
    white-space: normal;
    word-break: break-word;
}

.field-error {
    font-size: 12px;
    color: #dc3545;
    margin-top: 4px;
    padding-left: 4px;
    display: none;
    align-items: center;
    gap: 4px;
}

.field-error.visible {
    display: flex !important;
}

/* Phone prefix */
.phone-prefix {
    position: relative;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.phone-prefix-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 10px;
    border: none;
    border-right: 2px solid #e9ecef;
    background: transparent;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    color: #1a1a2e;
    white-space: nowrap;
    min-height: 48px;
    flex-shrink: 0;
    transition: background 0.2s;
}

.phone-prefix-btn:hover {
    background: #f0f1f3;
}

.phone-prefix-btn .fe-chevron-down {
    font-size: 12px;
    color: #adb5bd;
    transition: transform 0.2s;
}

.phone-prefix-btn.open .fe-chevron-down {
    transform: rotate(180deg);
}

.phone-prefix-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 200;
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    max-height: 260px;
    overflow-y: auto;
    min-width: 230px;
    display: none;
    list-style: none;
    padding: 4px;
    margin: 4px 0 0;
}

.phone-prefix-dropdown.show {
    display: block;
}

.phone-prefix-dropdown li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    cursor: pointer;
    border-radius: 6px;
    font-size: 14px;
    color: #1a1a2e;
    transition: background 0.15s;
}

.phone-prefix-dropdown li:hover {
    background: #f0f1f3;
}

.phone-prefix-dropdown li.active {
    background: #fff5e6;
    color: var(--login-accent);
    font-weight: 600;
}

.phone-prefix-dropdown li .pays-nom {
    flex: 1;
}

.phone-prefix-dropdown li .pays-code {
    color: #adb5bd;
    font-weight: 500;
}

.phone-prefix-dropdown::-webkit-scrollbar {
    width: 4px;
}
.phone-prefix-dropdown::-webkit-scrollbar-thumb {
    background: #e9ecef;
    border-radius: 4px;
}

.input-group-custom:has(.phone-prefix-btn) input.phone-input {
    padding-left: 8px;
}

input.phone-input:disabled {
    background: transparent;
    color: #adb5bd;
    cursor: default;
}

@media (max-width: 576px) {
    .phone-prefix-dropdown { min-width: 200px; max-height: 200px; }
    .phone-prefix-btn { padding: 12px 6px; font-size: 13px; }
}

/* Checkbox group (step 7) */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin: 16px 0;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: var(--login-input-radius);
    background: #fafbfc;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    border-color: var(--login-accent);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-label .checkbox-custom {
    width: 22px;
    height: 22px;
    min-width: 22px;
    border-radius: 6px;
    border: 2px solid #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    margin-top: 1px;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: var(--login-accent);
    border-color: var(--login-accent);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    color: #fff;
    font-size: 13px;
    font-weight: 700;
}

.checkbox-label:has(input:checked) {
    border-color: var(--login-accent);
    background: var(--login-accent-soft);
}

.checkbox-label .checkbox-content {
    flex: 1;
}

.checkbox-label .checkbox-content .checkbox-title {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a2e;
    display: block;
    margin-bottom: 2px;
}

.checkbox-label .checkbox-content .checkbox-desc {
    font-size: 12px;
    color: #6c757d;
    line-height: 1.4;
}

.checkbox-label .checkbox-content .checkbox-desc a {
    color: var(--login-accent);
    text-decoration: none;
    font-weight: 600;
}

.checkbox-label .checkbox-content .checkbox-desc a:hover {
    text-decoration: underline;
}

/* Summary grid (step 7) */
.summary-section-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--login-accent);
    margin: 16px 0 8px;
    padding-bottom: 6px;
    border-bottom: 2px solid #f0f0f0;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 4px;
}

.summary-item {
    padding: 10px 14px;
    background: #fafbfc;
    border: 1px solid #e9ecef;
    border-radius: 10px;
}

.summary-item .summary-label {
    font-size: 10px;
    color: #adb5bd;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 2px;
}

.summary-item .summary-value {
    font-size: 14px;
    color: #1a1a2e;
    font-weight: 500;
    word-break: break-word;
}

/* Toggle arrondissement/quartier button */
.toggle-field-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    color: #6c757d;
    font-size: 12px;
    cursor: pointer;
    padding: 4px 0;
    transition: color 0.2s;
    font-family: inherit;
}

.toggle-field-btn:hover {
    color: var(--login-accent);
}

/* Password mismatch */
.password-mismatch {
    font-size: 12px;
    color: #dc3545;
    margin-top: 4px;
    display: none;
    align-items: center;
    gap: 4px;
}

.password-mismatch.visible {
    display: flex;
}

/* Login footer */
.login-footer-link {
    display: block;
    text-align: center;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #f0f0f0;
    font-size: 14px;
    color: #6c757d;
}

.login-footer-link a {
    color: var(--login-accent);
    text-decoration: none;
    font-weight: 600;
}

.login-footer-link a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 576px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    .nav-buttons {
        flex-direction: column;
    }
    .nav-buttons .btn-prev,
    .nav-buttons .btn-next {
        width: 100%;
    }
    .radio-cards {
        flex-direction: column;
    }
    .radio-card {
        min-width: unset;
    }
    .radio-options-inline {
        flex-direction: column;
    }
    .upload-zone {
        min-height: 80px;
        padding: 16px 12px;
    }
    .upload-zone .upload-icon {
        font-size: 24px;
    }
    .summary-grid {
        grid-template-columns: 1fr;
    }
    .checkbox-label {
        padding: 12px 14px;
    }
}
</style>
@endpush

@section('content')
<div class="login-card" style="overflow-y: auto; height: 100%;">
    <div class="text-center">
        <a href="{{ url('/') }}" class="back-home-link"><i class="fe fe-arrow-left"></i> Retour à l'accueil</a>
    </div>

    <div class="login-logo">
        <a href="{{ url('/') }}">
            <img src="{{ url('frontend/asset/images/brand/logo-dark.png') }}" class="header-brand-img dark-logo" alt="Alogoto">
        </a>
    </div>

    <div class="login-title">
        <h2>Inscription Institution</h2>
        <p>Créez votre compte institutionnel en 7 étapes</p>
    </div>

    @if (session('status'))
        <div class="alert-success">
            <i class="fe fe-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            <i class="fe fe-alert-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- STEPS PROGRESS -->
    <div class="steps-progress" id="stepsProgress">
        <div class="step-indicator" data-step="1">
            <div class="step-dot active" data-step="1"><span class="step-number">1</span></div>
            <span class="step-label">Générales</span>
        </div>
        <div class="step-connector" data-step="2"></div>
        <div class="step-indicator" data-step="2">
            <div class="step-dot" data-step="2"><span class="step-number">2</span></div>
            <span class="step-label">Localisation</span>
        </div>
        <div class="step-connector" data-step="3"></div>
        <div class="step-indicator" data-step="3">
            <div class="step-dot" data-step="3"><span class="step-number">3</span></div>
            <span class="step-label">Contacts</span>
        </div>
        <div class="step-connector" data-step="4"></div>
        <div class="step-indicator" data-step="4">
            <div class="step-dot" data-step="4"><span class="step-number">4</span></div>
            <span class="step-label">Responsable</span>
        </div>
        <div class="step-connector" data-step="5"></div>
        <div class="step-indicator" data-step="5">
            <div class="step-dot" data-step="5"><span class="step-number">5</span></div>
            <span class="step-label">Documents</span>
        </div>
        <div class="step-connector" data-step="6"></div>
        <div class="step-indicator" data-step="6">
            <div class="step-dot" data-step="6"><span class="step-number">6</span></div>
            <span class="step-label">Sécurité</span>
        </div>
        <div class="step-connector" data-step="7"></div>
        <div class="step-indicator" data-step="7">
            <div class="step-dot" data-step="7"><span class="step-number">7</span></div>
            <span class="step-label">Validation</span>
        </div>
    </div>

    <!-- FORM -->
    <form method="POST" action="{{ route('register.institution.perform') }}" id="registerForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="role" value="institution">

        <!-- ==================== STEP 1 ==================== -->
        <div class="step-content active" data-step="1">
            <div class="step-title">Informations générales</div>
            <div class="step-subtitle">
                Identité officielle de votre institution
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nom officiel <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('nom_officiel') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-home"></i></span>
                        <input type="text" name="nom_officiel" id="nom_officiel" class="form-control-custom"
                               placeholder="Dénomination sociale" value="{{ old('nom_officiel') }}" required>
                    </div>
                    <div class="field-error" id="nom_officielError"></div>
                </div>
                <div class="form-group">
                    <label>Sigle / Acronyme</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-type"></i></span>
                        <input type="text" name="sigle" id="sigle" class="form-control-custom"
                               placeholder="Ex: IMF-ALO" value="{{ old('sigle') }}">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Type d'institution <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('type_institution') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-layers"></i></span>
                        <select name="type_institution" id="type_institution" class="form-control-custom" required>
                            <option value="">Sélectionnez le type</option>
                            <option value="association" @selected(old('type_institution') === 'association')>Association</option>
                            <option value="mutuelle" @selected(old('type_institution') === 'mutuelle')>Mutuelle</option>
                            <option value="cooperative" @selected(old('type_institution') === 'cooperative')>Coopérative</option>
                            <option value="ong" @selected(old('type_institution') === 'ong')>ONG</option>
                            <option value="fondation" @selected(old('type_institution') === 'fondation')>Fondation</option>
                            <option value="autre" @selected(old('type_institution') === 'autre')>Autre</option>
                        </select>
                    </div>
                    <div class="field-error" id="type_institutionError"></div>
                </div>
                <div class="form-group">
                    <label>RCCM <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('rccm') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-file-text"></i></span>
                        <input type="text" name="rccm" id="rccm" class="form-control-custom"
                               placeholder="Numéro RCCM" value="{{ old('rccm') }}" required>
                    </div>
                    <div class="field-error" id="rccmError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>IFU <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('ifu') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-hash"></i></span>
                        <input type="text" name="ifu" id="ifu" class="form-control-custom"
                               placeholder="Numéro IFU" value="{{ old('ifu') }}" required>
                    </div>
                    <div class="field-error" id="ifuError"></div>
                </div>
                <div class="form-group">
                    <label>Date de création <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('date_creation') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-calendar"></i></span>
                        <input type="date" name="date_creation" id="date_creation" class="form-control-custom"
                               value="{{ old('date_creation') }}" required>
                    </div>
                    <div class="field-error" id="date_creationError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Numéro d'agrément <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('agrement') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-award"></i></span>
                        <input type="text" name="agrement" id="agrement" class="form-control-custom"
                               placeholder="Numéro d'agrément" value="{{ old('agrement') }}" required>
                    </div>
                    <div class="field-error" id="agrementError"></div>
                </div>
                <div class="form-group">
                    <label>Secteur d'activité <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('secteur') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-grid"></i></span>
                        <select name="secteur" id="secteur" class="form-control-custom" required>
                            <option value="">Sélectionnez le secteur</option>
                            <option value="microfinance" @selected(old('secteur') === 'microfinance')>Microfinance</option>
                            <option value="finance" @selected(old('secteur') === 'finance')>Finance</option>
                            <option value="agriculture" @selected(old('secteur') === 'agriculture')>Agriculture</option>
                            <option value="education" @selected(old('secteur') === 'education')>Éducation</option>
                            <option value="sante" @selected(old('secteur') === 'sante')>Santé</option>
                            <option value="social" @selected(old('secteur') === 'social')>Social</option>
                            <option value="environnement" @selected(old('secteur') === 'environnement')>Environnement</option>
                            <option value="autre" @selected(old('secteur') === 'autre')>Autre</option>
                        </select>
                    </div>
                    <div class="field-error" id="secteurError"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Description / Objet social <span class="required-star">*</span></label>
                <div class="input-group-custom @error('description') is-invalid @enderror">
                    <span class="input-icon" style="align-items:flex-start;padding-top:14px;"><i class="fe fe-align-left"></i></span>
                    <textarea name="description" id="description" class="form-control-custom"
                              placeholder="Décrivez l'objet social et les missions de votre institution..." required>{{ old('description') }}</textarea>
                </div>
                <div class="field-error" id="descriptionError"></div>
            </div>

            <div class="nav-buttons">
                <div></div>
                <button type="button" class="btn-next" id="step1Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 2 ==================== -->
        <div class="step-content" data-step="2">
            <div class="step-title">Localisation</div>
            <div class="step-subtitle">
                Adresse géographique de votre institution
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Pays <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('pays_id') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-globe"></i></span>
                        <select name="pays_id" id="pays_id" class="form-control-custom location-select" required>
                            <option value="">Chargement...</option>
                        </select>
                        <span class="input-spinner" id="pays_idSpinner" style="display:none;"><i class="fe fe-loader fe-spin"></i></span>
                    </div>
                    <div class="field-error" id="pays_idError"></div>
                </div>
                <div class="form-group">
                    <label>Département <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map-pin"></i></span>
                        <select name="departement_id" id="departement_id" class="form-control-custom location-select" disabled required>
                            <option value="">Sélectionnez un pays d'abord</option>
                        </select>
                        <span class="input-spinner" id="departement_idSpinner" style="display:none;"><i class="fe fe-loader fe-spin"></i></span>
                    </div>
                    <div class="field-error" id="departement_idError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Commune <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map"></i></span>
                        <select name="commune_id" id="commune_id" class="form-control-custom location-select" disabled required>
                            <option value="">Sélectionnez un département d'abord</option>
                        </select>
                        <span class="input-spinner" id="commune_idSpinner" style="display:none;"><i class="fe fe-loader fe-spin"></i></span>
                    </div>
                    <div class="field-error" id="commune_idError"></div>
                </div>
                <div class="form-group">
                    <label>Arrondissement</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-layers"></i></span>
                        <select name="arrondissement_id" id="arrondissement_id" class="form-control-custom location-select" disabled>
                            <option value="">Sélectionnez une commune d'abord</option>
                        </select>
                        <input type="text" name="arrondissement_nom" id="arrondissement_nom" class="form-control-custom"
                               placeholder="Saisissez l'arrondissement" style="display:none;" disabled>
                        <span class="input-spinner" id="arrondissement_idSpinner" style="display:none;"><i class="fe fe-loader fe-spin"></i></span>
                    </div>
                    <div class="field-error" id="arrondissement_idError"></div>
                    <button type="button" id="toggleArrondissement" class="toggle-field-btn" style="display:none;" data-mode="select">
                        Je ne trouve pas mon arrondissement <i class="fe fe-edit"></i>
                    </button>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Quartier / Village <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map-pin"></i></span>
                        <select name="quartier_id" id="quartier_id" class="form-control-custom location-select" disabled required>
                            <option value="">Sélectionnez un arrondissement d'abord</option>
                        </select>
                        <input type="text" name="quartier_nom" id="quartier_nom" class="form-control-custom"
                               placeholder="Saisissez le quartier" style="display:none;" disabled>
                        <span class="input-spinner" id="quartier_idSpinner" style="display:none;"><i class="fe fe-loader fe-spin"></i></span>
                    </div>
                    <div class="field-error" id="quartier_idError"></div>
                    <button type="button" id="toggleQuartier" class="toggle-field-btn" style="display:none;" data-mode="select">
                        Je ne trouve pas mon quartier <i class="fe fe-edit"></i>
                    </button>
                </div>
                <div class="form-group">
                    <label>Adresse <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('adresse') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-navigation"></i></span>
                        <input type="text" name="adresse" id="adresse" class="form-control-custom"
                               placeholder="N° rue, quartier, ville..." value="{{ old('adresse') }}" required>
                    </div>
                    <div class="field-error" id="adresseError"></div>
                </div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(1)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="button" class="btn-next" id="step2Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 3 ==================== -->
        <div class="step-content" data-step="3">
            <div class="step-title">Contacts officiels</div>
            <div class="step-subtitle">
                Coordonnées officielles de l'institution
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email officiel <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('email_officiel') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-mail"></i></span>
                        <input type="email" name="email_officiel" id="email_officiel" class="form-control-custom"
                               placeholder="contact@institution.org" value="{{ old('email_officiel') }}" required>
                    </div>
                    <div class="field-error" id="email_officielError"></div>
                </div>
                <div class="form-group">
                    <label>Téléphone officiel <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('telephone') is-invalid @enderror">
                        <div class="phone-prefix">
                            <button type="button" class="phone-prefix-btn" id="phonePrefixBtn3" title="Choisir le pays">
                                <span class="phone-code" id="phoneCode3">+229</span>
                                <i class="fe fe-chevron-down"></i>
                            </button>
                            <ul class="phone-prefix-dropdown" id="phonePrefixDropdown3" role="listbox" aria-label="Indicatif téléphonique"></ul>
                        </div>
                        <input type="tel" id="telephone3" name="telephone" class="form-control-custom phone-input"
                               placeholder="Sélectionnez d'abord l'indicatif" disabled required>
                    </div>
                    <div class="field-error" id="telephone3Error"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone secondaire</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-phone"></i></span>
                        <input type="tel" name="telephone_secondaire" id="telephone_secondaire" class="form-control-custom"
                               placeholder="Optionnel" value="{{ old('telephone_secondaire') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Site web</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-globe"></i></span>
                        <input type="url" name="site_web" id="site_web" class="form-control-custom"
                               placeholder="https://www.institution.org" value="{{ old('site_web') }}">
                    </div>
                </div>
            </div>

            <div class="form-group" style="max-width:50%;">
                <label>Boîte postale</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="fe fe-inbox"></i></span>
                    <input type="text" name="bp" id="bp" class="form-control-custom"
                           placeholder="BP 0000" value="{{ old('bp') }}">
                </div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(2)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="button" class="btn-next" id="step3Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 4 ==================== -->
        <div class="step-content" data-step="4">
            <div class="step-title">Responsable principal</div>
            <div class="step-subtitle">
                Identité du dirigeant / représentant légal
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prénom <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_prenom') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-user"></i></span>
                        <input type="text" name="resp_prenom" id="resp_prenom" class="form-control-custom"
                               placeholder="Prénom du responsable" value="{{ old('resp_prenom') }}" required>
                    </div>
                    <div class="field-error" id="resp_prenomError"></div>
                </div>
                <div class="form-group">
                    <label>Nom <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_nom') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-user"></i></span>
                        <input type="text" name="resp_nom" id="resp_nom" class="form-control-custom"
                               placeholder="Nom du responsable" value="{{ old('resp_nom') }}" required>
                    </div>
                    <div class="field-error" id="resp_nomError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Fonction <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_fonction') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-briefcase"></i></span>
                        <input type="text" name="resp_fonction" id="resp_fonction" class="form-control-custom"
                               placeholder="Directeur, Président..." value="{{ old('resp_fonction') }}" required>
                    </div>
                    <div class="field-error" id="resp_fonctionError"></div>
                </div>
                <div class="form-group">
                    <label>Sexe <span class="required-star">*</span></label>
                    <div class="radio-options-inline" id="sexeGroup">
                        <label class="radio-option-inline">
                            <input type="radio" name="resp_sexe" value="M" @checked(old('resp_sexe') === 'M')>
                            <span class="radio-custom-circle"></span>
                            <span>Masculin</span>
                        </label>
                        <label class="radio-option-inline">
                            <input type="radio" name="resp_sexe" value="F" @checked(old('resp_sexe') === 'F')>
                            <span class="radio-custom-circle"></span>
                            <span>Féminin</span>
                        </label>
                    </div>
                    <div class="field-error" id="resp_sexeError"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date de naissance <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_date_naissance') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-calendar"></i></span>
                        <input type="date" name="resp_date_naissance" id="resp_date_naissance" class="form-control-custom"
                               value="{{ old('resp_date_naissance') }}" required>
                    </div>
                    <div class="field-error" id="resp_date_naissanceError"></div>
                </div>
                <div class="form-group">
                    <label>Nationalité <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_nationalite') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-flag"></i></span>
                        <input type="text" name="resp_nationalite" id="resp_nationalite" class="form-control-custom"
                               placeholder="Nationalité" value="{{ old('resp_nationalite') }}" required>
                    </div>
                    <div class="field-error" id="resp_nationaliteError"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Type de pièce d'identité <span class="required-star">*</span></label>
                <div class="radio-cards" id="typePieceGroup">
                    <div class="radio-card">
                        <input type="radio" name="resp_type_piece" id="piece_cnib" value="cnib" @checked(old('resp_type_piece') === 'cnib')>
                        <label for="piece_cnib"><i class="fe fe-credit-card"></i> CNIB</label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" name="resp_type_piece" id="piece_passeport" value="passeport" @checked(old('resp_type_piece') === 'passeport')>
                        <label for="piece_passeport"><i class="fe fe-book"></i> Passeport</label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" name="resp_type_piece" id="piece_permis" value="permis" @checked(old('resp_type_piece') === 'permis')>
                        <label for="piece_permis"><i class="fe fe-truck"></i> Permis</label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" name="resp_type_piece" id="piece_autre" value="autre" @checked(old('resp_type_piece') === 'autre')>
                        <label for="piece_autre"><i class="fe fe-file"></i> Autre</label>
                    </div>
                </div>
                <div class="field-error" id="resp_type_pieceError"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Numéro de pièce <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_numero_piece') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-credit-card"></i></span>
                        <input type="text" name="resp_numero_piece" id="resp_numero_piece" class="form-control-custom"
                               placeholder="Numéro de la pièce d'identité" value="{{ old('resp_numero_piece') }}" required>
                    </div>
                    <div class="field-error" id="resp_numero_pieceError"></div>
                </div>
                <div class="form-group">
                    <label>Téléphone du responsable <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('resp_telephone') is-invalid @enderror">
                        <div class="phone-prefix">
                            <button type="button" class="phone-prefix-btn" id="phonePrefixBtn4" title="Choisir le pays">
                                <span class="phone-code" id="phoneCode4">+229</span>
                                <i class="fe fe-chevron-down"></i>
                            </button>
                            <ul class="phone-prefix-dropdown" id="phonePrefixDropdown4" role="listbox" aria-label="Indicatif téléphonique"></ul>
                        </div>
                        <input type="tel" id="telephone4" name="resp_telephone" class="form-control-custom phone-input"
                               placeholder="Sélectionnez d'abord l'indicatif" disabled required>
                    </div>
                    <div class="field-error" id="telephone4Error"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Email du responsable <span class="required-star">*</span></label>
                <div class="input-group-custom @error('resp_email') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-mail"></i></span>
                    <input type="email" name="resp_email" id="resp_email" class="form-control-custom"
                           placeholder="responsable@institution.org" value="{{ old('resp_email') }}" required>
                </div>
                <div class="field-error" id="resp_emailError"></div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(3)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="button" class="btn-next" id="step4Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 5 ==================== -->
        <div class="step-content" data-step="5">
            <div class="step-title">Documents obligatoires</div>
            <div class="step-subtitle">
                Veuillez fournir les documents requis
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div id="docs-container"></div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(4)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="button" class="btn-next" id="step5Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 6 ==================== -->
        <div class="step-content" data-step="6">
            <div class="step-title">Sécurité</div>
            <div class="step-subtitle">
                Créez vos identifiants de connexion
                <span class="required-hint"><span class="required-star">*</span> Champs obligatoires</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Adresse email (connexion) <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('email') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-at-sign"></i></span>
                        <input type="email" name="email" id="email" class="form-control-custom"
                               placeholder="votre@email.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="field-error" id="emailError"></div>
                    <div class="field-error" id="emailUniqueError" style="display:none;"></div>
                </div>
                <div class="form-group">
                    <label>Nom d'utilisateur <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('name') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-user"></i></span>
                        <input type="text" name="name" id="name" class="form-control-custom"
                               placeholder="Nom d'utilisateur" value="{{ old('name') }}" required>
                    </div>
                    <div class="field-error" id="nameError"></div>
                    <div class="field-error" id="nameUniqueError" style="display:none;"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('password') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control-custom"
                               placeholder="Min. 8 caractères" minlength="8" required>
                        <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Afficher le mot de passe">
                            <i class="fe fe-eye-off"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                    <div class="field-error" id="passwordError"></div>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('password_confirmation') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control-custom" placeholder="Répétez le mot de passe" minlength="8" required>
                    </div>
                    <div class="password-mismatch" id="passwordMismatch">
                        <i class="fe fe-alert-circle"></i> Les mots de passe ne correspondent pas
                    </div>
                    <div class="field-error" id="password_confirmationError"></div>
                </div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(5)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="button" class="btn-next" id="step6Next" disabled>Suivant <i class="fe fe-arrow-right"></i></button>
            </div>
        </div>

        <!-- ==================== STEP 7 ==================== -->
        <div class="step-content" data-step="7">
            <div class="step-title">Validation</div>
            <div class="step-subtitle">Vérifiez vos informations et acceptez les conditions</div>

            <div class="summary-section-title">Informations générales</div>
            <div class="summary-grid" id="summaryGeneral"></div>

            <div class="summary-section-title">Localisation</div>
            <div class="summary-grid" id="summaryLocation"></div>

            <div class="summary-section-title">Responsable</div>
            <div class="summary-grid" id="summaryResponsable"></div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="accept_cgu" id="accept_cgu" required>
                    <span class="checkbox-custom"></span>
                    <div class="checkbox-content">
                        <span class="checkbox-title">Conditions générales d'utilisation</span>
                        <span class="checkbox-desc">
                            J'accepte les <a href="#" target="_blank">conditions générales d'utilisation</a> de la plateforme
                        </span>
                    </div>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="accept_confidentialite" id="accept_confidentialite" required>
                    <span class="checkbox-custom"></span>
                    <div class="checkbox-content">
                        <span class="checkbox-title">Politique de confidentialité</span>
                        <span class="checkbox-desc">
                            J'accepte la <a href="#" target="_blank">politique de confidentialité</a> et le traitement de mes données
                        </span>
                    </div>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="confirm_exactitude" id="confirm_exactitude" required>
                    <span class="checkbox-custom"></span>
                    <div class="checkbox-content">
                        <span class="checkbox-title">Exactitude des informations</span>
                        <span class="checkbox-desc">
                            Je certifie sur l'honneur l'exactitude et la sincérité de toutes les informations fournies
                        </span>
                    </div>
                </label>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(6)"><i class="fe fe-arrow-left"></i> Retour</button>
                <button type="submit" class="btn-next" id="register-submit" disabled>
                    <span class="btn-text">Créer mon compte institution</span>
                    <span class="spinner"></span>
                </button>
            </div>
        </div>
    </form>

    <div class="login-footer-link">
        Déjà inscrit ? <a href="{{ route('login.institution') }}">Connectez-vous</a>
    </div>
</div>

<script id="phoneCountriesData" type="application/json">
    @if(isset($phoneCountries) && $phoneCountries->count())
        @php
            $countries = [];
            foreach($phoneCountries as $p) {
                $countries[] = ['id' => $p->id, 'code' => $p->code, 'nom' => $p->nom, 'indicatif' => $p->indicatif];
            }
        @endphp
        {!! json_encode($countries) !!}
    @else
        []
    @endif
</script>
@php
    use App\Models\DocumentRule;
    $instDocRules = DocumentRule::active()->forContext('registration')->forRole('institution')->ordered()->get(['slug', 'label', 'description', 'obligatoire', 'types_mime', 'max_size'])->toArray();
@endphp
<script id="docRulesData" type="application/json">@json($instDocRules)</script>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    var currentStep = 1;
    var TOTAL_STEPS = 7;

    window.goToStep = function(step) {
        if (step < 1 || step > TOTAL_STEPS) return;
        if (step > currentStep) {
            if (!validateStep(currentStep)) {
                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }
        }
        currentStep = step;
        updateUI();
    };

    function updateUI() {
        document.querySelectorAll('.step-content').forEach(function(el) {
            el.classList.toggle('active', parseInt(el.dataset.step) === currentStep);
        });
        document.querySelectorAll('.step-dot').forEach(function(dot) {
            var s = parseInt(dot.dataset.step);
            dot.classList.remove('active', 'completed');
            if (s === currentStep) dot.classList.add('active');
            else if (s < currentStep) dot.classList.add('completed');
        });
        document.querySelectorAll('.step-connector').forEach(function(conn) {
            var s = parseInt(conn.dataset.step);
            conn.classList.toggle('completed', s <= currentStep);
        });
        var card = document.querySelector('.login-card');
        if (card) card.scrollTo({ top: 0, behavior: 'smooth' });
        if (currentStep === 7) buildSummary();
    }

    window.validateStep = function(step) {
        var valid = true;
        if (step === 1) {
            var fields = ['nom_officiel', 'type_institution', 'rccm', 'ifu', 'date_creation', 'agrement', 'secteur', 'description'];
            fields.forEach(function(id) {
                var el = document.getElementById(id);
                if (el && !el.value.trim()) {
                    el.closest('.input-group-custom')?.classList.add('is-invalid');
                    valid = false;
                } else if (el) {
                    el.closest('.input-group-custom')?.classList.remove('is-invalid');
                }
            });
        }
        if (step === 2) {
            if (!isStep2Valid()) valid = false;
        }
        if (step === 3) {
            var emailOff = document.getElementById('email_officiel');
            if (emailOff && !emailOff.value.trim()) {
                emailOff.closest('.input-group-custom')?.classList.add('is-invalid');
                valid = false;
            }
            if (!validatePhoneField3()) valid = false;
        }
        if (step === 4) {
            var respFields = ['resp_prenom', 'resp_nom', 'resp_fonction', 'resp_date_naissance', 'resp_nationalite', 'resp_numero_piece', 'resp_email'];
            respFields.forEach(function(id) {
                var el = document.getElementById(id);
                if (el && !el.value.trim()) {
                    el.closest('.input-group-custom')?.classList.add('is-invalid');
                    valid = false;
                } else if (el) {
                    el.closest('.input-group-custom')?.classList.remove('is-invalid');
                }
            });
            var sexe = document.querySelector('input[name="resp_sexe"]:checked');
            if (!sexe) { document.getElementById('sexeGroup')?.classList.add('is-invalid'); valid = false; }
            var typePiece = document.querySelector('input[name="resp_type_piece"]:checked');
            if (!typePiece) { document.getElementById('typePieceGroup')?.classList.add('is-invalid'); valid = false; }
            if (!validatePhoneField4()) valid = false;
        }
        if (step === 5) {
            var rules = [];
            try { var el = document.getElementById('docRulesData'); if (el) rules = JSON.parse(el.textContent); } catch(e) {}
            rules.forEach(function(rule) {
                if (rule.obligatoire !== false) {
                    var input = document.getElementById(rule.slug);
                    if (!input || !input.files || !input.files.length) valid = false;
                }
            });
        }
        if (!valid) showToast('Veuillez remplir tous les champs obligatoires.', 'error');
        return valid;
    };

    // ===================== PASSWORD =====================
    var pwInput = document.getElementById('password');
    var strengthEl = document.getElementById('passwordStrength');
    var strengthText = document.getElementById('strengthText');
    var bars = strengthEl.querySelectorAll('span');

    pwInput.addEventListener('input', function() {
        var val = this.value;
        if (val.length === 0) { strengthEl.classList.remove('visible'); return; }
        strengthEl.classList.add('visible');
        var score = 0;
        if (val.length >= 8) score++;
        if (val.length >= 12) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;
        var levels = ['', 'Faible', 'Moyen', 'Fort', 'Très fort'];
        var classes = ['', 'weak', 'medium', 'strong', 'very-strong'];
        var level = Math.min(Math.floor(score / 1.25) + 1, 4);
        bars.forEach(function(bar, i) {
            bar.className = i < level ? 'active ' + classes[level] : '';
        });
        strengthText.textContent = levels[level] || '';
        strengthText.className = 'strength-text ' + (classes[level] || '');
        updateStep6Button();
    });

    document.getElementById('togglePassword').addEventListener('click', function() {
        var pw = document.getElementById('password');
        var icon = this.querySelector('i');
        if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fe fe-eye'; }
        else { pw.type = 'password'; icon.className = 'fe fe-eye-off'; }
    });

    document.getElementById('password_confirmation').addEventListener('input', function() {
        var pw = document.getElementById('password').value;
        var mismatchEl = document.getElementById('passwordMismatch');
        if (this.value && pw !== this.value) mismatchEl.style.display = 'flex';
        else mismatchEl.style.display = 'none';
        updateStep6Button();
    });

    // ===================== STEP 1 =====================
    var STEP1_INPUTS = ['nom_officiel', 'type_institution', 'rccm', 'ifu', 'date_creation', 'agrement', 'secteur', 'description'];
    for (var i = 0; i < STEP1_INPUTS.length; i++) {
        var el = document.getElementById(STEP1_INPUTS[i]);
        if (el) {
            el.addEventListener('input', updateStep1Button);
            el.addEventListener('change', updateStep1Button);
        }
    }

    function updateStep1Button() {
        var btn = document.getElementById('step1Next');
        if (!btn) return;
        var allOk = true;
        STEP1_INPUTS.forEach(function(id) {
            var field = document.getElementById(id);
            if (!field || !field.value.trim()) allOk = false;
        });
        btn.disabled = !allOk;
    }

    document.getElementById('step1Next').addEventListener('click', function() {
        if (validateStep(1)) goToStep(2);
    });

    // ===================== UNIQUENESS =====================
    var UNIQUE_FIELDS = ['email', 'name'];
    for (var u = 0; u < UNIQUE_FIELDS.length; u++) {
        (function(field) {
            var el = document.getElementById(field);
            if (!el) return;
            el.addEventListener('blur', function() {
                var value = this.value.trim();
                if (!value || value.length < 2) return;
                var errorEl = document.getElementById(field + 'UniqueError');
                if (!errorEl) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'field-error';
                    errorEl.id = field + 'UniqueError';
                    errorEl.style.display = 'none';
                    this.parentNode.appendChild(errorEl);
                }
                var url = '/api/v1/check-uniqueness?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value);
                fetch(url)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var group = el.closest('.input-group-custom');
                        if (data.exists) {
                            if (group) group.classList.add('is-invalid');
                            errorEl.textContent = (field === 'email' ? 'cet email' : 'ce nom') + ' est déjà utilisé';
                            errorEl.classList.add('visible');
                            errorEl.style.display = 'flex';
                        }
                    })
                    .catch(function() {});
            });
            el.addEventListener('input', function() {
                var errorEl = document.getElementById(field + 'UniqueError');
                if (errorEl && errorEl.style.display !== 'none') {
                    var group = this.closest('.input-group-custom');
                    if (group) group.classList.remove('is-invalid');
                    errorEl.textContent = '';
                    errorEl.classList.remove('visible');
                    errorEl.style.display = 'none';
                }
            });
        })(UNIQUE_FIELDS[u]);
    }

    // ===================== FILE UPLOAD =====================
    function handleFileUpload(inputId, previewId, imgId, filenameId, errorId, zoneId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        var img = document.getElementById(imgId);
        var filename = document.getElementById(filenameId);
        var error = document.getElementById(errorId);
        var zone = document.getElementById(zoneId);
        if (!input || !zone) return;

        input.addEventListener('change', function(e) {
            var file = this.files && this.files[0];
            if (!file) { updateStep5Button(); return; }
            if (error) error.classList.remove('visible');
            var maxSizeMb = parseFloat(this.getAttribute('data-max-size')) || 5;
            var maxSizeBytes = maxSizeMb * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                if (error) { error.textContent = 'Le fichier ne doit pas depasser ' + maxSizeMb + ' Mo.'; error.classList.add('visible'); }
                this.value = '';
                updateStep5Button();
                return;
            }
            var typesAttr = this.getAttribute('data-types') || 'jpg,jpeg,png,pdf';
            var allowedMimeMap = { 'jpg': 'image/jpeg', 'jpeg': 'image/jpeg', 'png': 'image/png', 'gif': 'image/gif', 'webp': 'image/webp', 'pdf': 'application/pdf', 'doc': 'application/msword', 'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' };
            var allowedTypesList = typesAttr.split(',').map(function(t) { return allowedMimeMap[t.trim().toLowerCase()]; }).filter(Boolean);
            if (allowedTypesList.length && !allowedTypesList.includes(file.type)) {
                if (error) { error.textContent = 'Format accepté: ' + typesAttr.toUpperCase() + '.'; error.classList.add('visible'); }
                this.value = '';
                updateStep5Button();
                return;
            }
            if (filename) filename.textContent = file.name;
            if (zone) zone.classList.add('has-file');
            if (preview) { preview.classList.remove('visible'); if (file.type.startsWith('image/')) { var reader = new FileReader(); reader.onload = function(ev) { img.src = ev.target.result; preview.classList.add('visible'); }; reader.readAsDataURL(file); } }
            updateStep5Button();
        });

        zone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
        zone.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('dragover'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; input.dispatchEvent(new Event('change')); }
        });
    }

    // Initialize document upload zones from rules
    function initDocUploads() {
        var rules = [];
        try { var scriptEl = document.getElementById('docRulesData'); if (scriptEl) rules = JSON.parse(scriptEl.textContent); } catch(e) {}
        var container = document.getElementById('docs-container');
        if (!container || !rules.length) return;
        container.innerHTML = '';
        var currentRow = null;
        rules.forEach(function(rule, index) {
            var slug = rule.slug;
            var label = rule.label || slug;
            var obligatoire = rule.obligatoire !== false;
            var types = rule.types_mime || 'jpg,jpeg,png,pdf';
            var maxSize = rule.max_size || 5;
            var accept = '.' + types.replace(/,/g, ',.');
            var isImageOnly = types.split(',').every(function(t) { return t.trim().match(/^(jpg|jpeg|png|gif|webp)$/i); });
            var icon = isImageOnly ? 'fe fe-image' : 'fe fe-upload-cloud';
            var hint = types.toUpperCase() + ' (max ' + maxSize + ' Mo)';

            var col = document.createElement('div');
            col.className = 'form-group';
            col.innerHTML =
                '<label>' + label + (obligatoire ? ' <span class="required-star">*</span>' : '') + '</label>' +
                '<div class="upload-zone" id="' + slug + 'Zone">' +
                    '<input type="file" id="' + slug + '" name="' + slug + '" accept="' + accept + '" data-max-size="' + maxSize + '" data-types="' + types + '">' +
                    '<div class="upload-icon"><i class="' + icon + '"></i></div>' +
                    '<div class="upload-text">Cliquez ou glissez-déposez <strong>' + label.toLowerCase() + '</strong></div>' +
                    '<div class="upload-hint">' + hint + '</div>' +
                    '<div class="upload-preview" id="' + slug + 'Preview">' +
                        '<img id="' + slug + 'Img" src="" alt="' + label + '">' +
                        '<button type="button" class="remove-file" onclick="removeFile(\'' + slug + '\')"><i class="fe fe-x"></i></button>' +
                    '</div>' +
                    '<div class="upload-filename" id="' + slug + 'Filename"></div>' +
                    '<div class="field-error" id="' + slug + 'Error"></div>' +
                '</div>';

            if (index % 2 === 0) {
                currentRow = document.createElement('div');
                currentRow.className = 'form-row';
                container.appendChild(currentRow);
            }
            currentRow.appendChild(col);

            handleFileUpload(slug, slug + 'Preview', slug + 'Img', slug + 'Filename', slug + 'Error', slug + 'Zone');
        });
        updateStep5Button();
    }

    initDocUploads();

    window.removeFile = function(inputId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(inputId + 'Preview');
        var filename = document.getElementById(inputId + 'Filename');
        var error = document.getElementById(inputId + 'Error');
        var zone = document.getElementById(inputId + 'Zone');
        if (input) input.value = '';
        if (preview) preview.classList.remove('visible');
        if (filename) filename.textContent = '';
        if (error) error.classList.remove('visible');
        if (zone) zone.classList.remove('has-file');
        var img = document.getElementById(inputId + 'Img');
        if (img) img.src = '';
        updateStep5Button();
    };

    function updateStep5Button() {
        var btn = document.getElementById('step5Next');
        if (!btn) return;
        var rules = [];
        try { var el = document.getElementById('docRulesData'); if (el) rules = JSON.parse(el.textContent); } catch(e) {}
        var allOk = true;
        rules.forEach(function(rule) {
            if (rule.obligatoire !== false) {
                var input = document.getElementById(rule.slug);
                if (!input || !input.files || !input.files.length) allOk = false;
            }
        });
        btn.disabled = !allOk;
    }

    document.getElementById('step5Next').addEventListener('click', function() {
        if (validateStep(5)) goToStep(6);
    });

    // ===================== PHONE COUNTRIES =====================
    var phoneCountries = [];
    try { var scriptEl = document.getElementById('phoneCountriesData'); if (scriptEl) phoneCountries = JSON.parse(scriptEl.textContent); } catch(e) {}
    var EXTRA_PREFIXES = [
        { code: 'FR', nom: 'France', indicatif: '+33' },
        { code: 'BE', nom: 'Belgique', indicatif: '+32' },
        { code: 'CH', nom: 'Suisse', indicatif: '+41' },
        { code: 'CA', nom: 'Canada', indicatif: '+1' },
        { code: 'US', nom: 'États-Unis', indicatif: '+1' },
        { code: 'CM', nom: 'Cameroun', indicatif: '+237' },
        { code: 'CI', nom: "Côte d'Ivoire", indicatif: '+225' },
        { code: 'SN', nom: 'Sénégal', indicatif: '+221' },
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
    function getPhoneRule(ind) { return PHONE_RULES[ind] || null; }

    // ===================== PHONE 3 (institution) =====================
    var selectedPrefix3 = null;
    var prefixBtn3 = document.getElementById('phonePrefixBtn3');
    var prefixDropdown3 = document.getElementById('phonePrefixDropdown3');
    var phoneCode3 = document.getElementById('phoneCode3');
    var phoneInput3 = document.getElementById('telephone3');
    var phoneError3 = document.getElementById('telephone3Error');

    function buildPhoneDropdown3() {
        prefixDropdown3.innerHTML = '';
        phoneCountries.forEach(function(c) {
            var li = document.createElement('li');
            li.dataset.code = c.code;
            li.dataset.indicatif = c.indicatif;
            li.setAttribute('role', 'option');
            li.innerHTML = '<span class="pays-nom">' + (c.nom || '') + '</span><span class="pays-code">' + (c.indicatif || '') + '</span>';
            li.addEventListener('click', function(e) {
                e.stopPropagation();
                selectPhonePrefix3(c.indicatif);
                closePhoneDropdown3();
            });
            prefixDropdown3.appendChild(li);
        });
    }

    function selectPhonePrefix3(indicatif) {
        selectedPrefix3 = indicatif;
        phoneCode3.textContent = indicatif;
        prefixBtn3.classList.add('has-prefix');
        phoneInput3.disabled = false;
        phoneInput3.value = '';
        var rule = getPhoneRule(indicatif);
        phoneInput3.placeholder = rule ? (indicatif === '+229' ? '01 XX XX XX XX' : 'XX XX XX XX') : 'XX XX XX XX';
        phoneInput3.focus();
        validatePhoneField3();
        updateStep3Button();
    }

    function togglePhoneDropdown3() {
        if (prefixDropdown3.classList.contains('show')) closePhoneDropdown3();
        else openPhoneDropdown3();
    }

    function openPhoneDropdown3() {
        prefixDropdown3.classList.add('show');
        prefixBtn3.classList.add('open');
        prefixDropdown3.querySelectorAll('li').forEach(function(li) {
            li.classList.toggle('active', li.dataset.indicatif === selectedPrefix3);
        });
        document.addEventListener('click', closePhoneDropdown3Outside);
    }

    function closePhoneDropdown3() {
        prefixDropdown3.classList.remove('show');
        prefixBtn3.classList.remove('open');
        document.removeEventListener('click', closePhoneDropdown3Outside);
    }

    function closePhoneDropdown3Outside(e) {
        if (!prefixBtn3.contains(e.target) && !prefixDropdown3.contains(e.target)) closePhoneDropdown3();
    }

    prefixBtn3.addEventListener('click', togglePhoneDropdown3);

    phoneInput3.addEventListener('input', function() {
        var raw = this.value.replace(/[^0-9]/g, '');
        var formatted = '';
        for (var i = 0; i < raw.length; i++) {
            if (i > 0 && i % 2 === 0) formatted += ' ';
            formatted += raw[i];
        }
        this.value = formatted;
        validatePhoneField3();
        updateStep3Button();
    });

    function getPhoneDigits3() { return phoneInput3.value.replace(/\s/g, ''); }

    function getFullPhone3() { return selectedPrefix3 ? selectedPrefix3 + getPhoneDigits3() : ''; }

    function validatePhoneField3() {
        var digits = getPhoneDigits3();
        if (!selectedPrefix3) { showPhoneError3('Veuillez sélectionner l\'indicatif du pays'); return false; }
        if (phoneInput3.disabled) { showPhoneError3(''); return true; }
        if (digits.length === 0) { showPhoneError3('Numéro de téléphone requis'); return false; }
        var rule = getPhoneRule(selectedPrefix3);
        var minLen = rule ? rule.min : 6;
        var maxLen = rule ? rule.max : 12;
        if (digits.length < minLen) { showPhoneError3('Numéro trop court (' + minLen + ' chiffres requis)'); return false; }
        if (digits.length > maxLen) { showPhoneError3('Numéro trop long (' + maxLen + ' chiffres max)'); return false; }
        showPhoneError3(''); return true;
    }

    function showPhoneError3(msg) {
        var group = phoneInput3 ? phoneInput3.closest('.input-group-custom') : null;
        if (group) group.classList.toggle('is-invalid', !!msg);
        if (phoneError3) {
            if (msg) { phoneError3.textContent = msg; phoneError3.classList.add('visible'); phoneError3.style.display = 'flex'; }
            else { phoneError3.textContent = ''; phoneError3.classList.remove('visible'); phoneError3.style.display = 'none'; }
        }
    }

    function updateStep3Button() {
        var btn = document.getElementById('step3Next');
        if (!btn) return;
        var emailOff = document.getElementById('email_officiel');
        var telOk = validatePhoneField3();
        btn.disabled = !(emailOff && emailOff.value.trim() && telOk);
    }

    buildPhoneDropdown3();
    document.getElementById('email_officiel').addEventListener('input', updateStep3Button);
    document.getElementById('step3Next').addEventListener('click', function() {
        if (validateStep(3)) goToStep(4);
    });

    // ===================== PHONE 4 (responsable) =====================
    var selectedPrefix4 = null;
    var prefixBtn4 = document.getElementById('phonePrefixBtn4');
    var prefixDropdown4 = document.getElementById('phonePrefixDropdown4');
    var phoneCode4 = document.getElementById('phoneCode4');
    var phoneInput4 = document.getElementById('telephone4');
    var phoneError4 = document.getElementById('telephone4Error');

    function buildPhoneDropdown4() {
        prefixDropdown4.innerHTML = '';
        phoneCountries.forEach(function(c) {
            var li = document.createElement('li');
            li.dataset.code = c.code;
            li.dataset.indicatif = c.indicatif;
            li.setAttribute('role', 'option');
            li.innerHTML = '<span class="pays-nom">' + (c.nom || '') + '</span><span class="pays-code">' + (c.indicatif || '') + '</span>';
            li.addEventListener('click', function(e) {
                e.stopPropagation();
                selectPhonePrefix4(c.indicatif);
                closePhoneDropdown4();
            });
            prefixDropdown4.appendChild(li);
        });
    }

    function selectPhonePrefix4(indicatif) {
        selectedPrefix4 = indicatif;
        phoneCode4.textContent = indicatif;
        prefixBtn4.classList.add('has-prefix');
        phoneInput4.disabled = false;
        phoneInput4.value = '';
        var rule = getPhoneRule(indicatif);
        phoneInput4.placeholder = rule ? (indicatif === '+229' ? '01 XX XX XX XX' : 'XX XX XX XX') : 'XX XX XX XX';
        phoneInput4.focus();
        validatePhoneField4();
        updateStep4Button();
    }

    function togglePhoneDropdown4() {
        if (prefixDropdown4.classList.contains('show')) closePhoneDropdown4();
        else openPhoneDropdown4();
    }

    function openPhoneDropdown4() {
        prefixDropdown4.classList.add('show');
        prefixBtn4.classList.add('open');
        prefixDropdown4.querySelectorAll('li').forEach(function(li) {
            li.classList.toggle('active', li.dataset.indicatif === selectedPrefix4);
        });
        document.addEventListener('click', closePhoneDropdown4Outside);
    }

    function closePhoneDropdown4() {
        prefixDropdown4.classList.remove('show');
        prefixBtn4.classList.remove('open');
        document.removeEventListener('click', closePhoneDropdown4Outside);
    }

    function closePhoneDropdown4Outside(e) {
        if (!prefixBtn4.contains(e.target) && !prefixDropdown4.contains(e.target)) closePhoneDropdown4();
    }

    prefixBtn4.addEventListener('click', togglePhoneDropdown4);

    phoneInput4.addEventListener('input', function() {
        var raw = this.value.replace(/[^0-9]/g, '');
        var formatted = '';
        for (var i = 0; i < raw.length; i++) {
            if (i > 0 && i % 2 === 0) formatted += ' ';
            formatted += raw[i];
        }
        this.value = formatted;
        validatePhoneField4();
        updateStep4Button();
    });

    function getPhoneDigits4() { return phoneInput4.value.replace(/\s/g, ''); }

    function getFullPhone4() { return selectedPrefix4 ? selectedPrefix4 + getPhoneDigits4() : ''; }

    function validatePhoneField4() {
        var digits = getPhoneDigits4();
        if (!selectedPrefix4) { showPhoneError4('Veuillez sélectionner l\'indicatif du pays'); return false; }
        if (phoneInput4.disabled) { showPhoneError4(''); return true; }
        if (digits.length === 0) { showPhoneError4('Numéro de téléphone requis'); return false; }
        var rule = getPhoneRule(selectedPrefix4);
        var minLen = rule ? rule.min : 6;
        var maxLen = rule ? rule.max : 12;
        if (digits.length < minLen) { showPhoneError4('Numéro trop court (' + minLen + ' chiffres requis)'); return false; }
        if (digits.length > maxLen) { showPhoneError4('Numéro trop long (' + maxLen + ' chiffres max)'); return false; }
        showPhoneError4(''); return true;
    }

    function showPhoneError4(msg) {
        var group = phoneInput4 ? phoneInput4.closest('.input-group-custom') : null;
        if (group) group.classList.toggle('is-invalid', !!msg);
        if (phoneError4) {
            if (msg) { phoneError4.textContent = msg; phoneError4.classList.add('visible'); phoneError4.style.display = 'flex'; }
            else { phoneError4.textContent = ''; phoneError4.classList.remove('visible'); phoneError4.style.display = 'none'; }
        }
    }

    function updateStep4Button() {
        var btn = document.getElementById('step4Next');
        if (!btn) return;
        var respFields = ['resp_prenom', 'resp_nom', 'resp_fonction', 'resp_date_naissance', 'resp_nationalite', 'resp_numero_piece', 'resp_email'];
        var allOk = true;
        respFields.forEach(function(id) { var el = document.getElementById(id); if (!el || !el.value.trim()) allOk = false; });
        if (!document.querySelector('input[name="resp_sexe"]:checked')) allOk = false;
        if (!document.querySelector('input[name="resp_type_piece"]:checked')) allOk = false;
        if (!validatePhoneField4()) allOk = false;
        btn.disabled = !allOk;
    }

    buildPhoneDropdown4();

    var STEP4_INPUTS = ['resp_prenom', 'resp_nom', 'resp_fonction', 'resp_date_naissance', 'resp_nationalite', 'resp_numero_piece', 'resp_email'];
    for (var i4 = 0; i4 < STEP4_INPUTS.length; i4++) {
        var el4 = document.getElementById(STEP4_INPUTS[i4]);
        if (el4) { el4.addEventListener('input', updateStep4Button); el4.addEventListener('change', updateStep4Button); }
    }
    document.querySelectorAll('input[name="resp_sexe"]').forEach(function(el) {
        el.addEventListener('change', function() { document.getElementById('sexeGroup')?.classList.remove('is-invalid'); updateStep4Button(); });
    });
    document.querySelectorAll('input[name="resp_type_piece"]').forEach(function(el) {
        el.addEventListener('change', function() { document.getElementById('typePieceGroup')?.classList.remove('is-invalid'); updateStep4Button(); });
    });
    document.getElementById('step4Next').addEventListener('click', function() {
        if (validateStep(4)) goToStep(5);
    });

    // ===================== STEP 6 =====================
    function updateStep6Button() {
        var btn = document.getElementById('step6Next');
        if (!btn) return;
        var email = document.getElementById('email');
        var name = document.getElementById('name');
        var pw = document.getElementById('password');
        var pw2 = document.getElementById('password_confirmation');
        btn.disabled = !(email && email.value.trim() && name && name.value.trim() && pw && pw.value.length >= 8 && pw2 && pw.value === pw2.value);
    }

    var STEP6_INPUTS = ['email', 'name', 'password', 'password_confirmation'];
    for (var i6 = 0; i6 < STEP6_INPUTS.length; i6++) {
        var el6 = document.getElementById(STEP6_INPUTS[i6]);
        if (el6) el6.addEventListener('input', updateStep6Button);
    }
    document.getElementById('step6Next').addEventListener('click', function() {
        if (validateStep(6)) goToStep(7);
    });

    // ===================== STEP 7: SUMMARY =====================
    var SUMMARY_LABELS = {
        nom_officiel: 'Nom officiel', sigle: 'Sigle', type_institution: "Type d'institution",
        rccm: 'RCCM', ifu: 'IFU', date_creation: 'Date de création', agrement: "Agrément",
        secteur: "Secteur", adresse: 'Adresse',
        resp_prenom: 'Prénom', resp_nom: 'Nom', resp_fonction: 'Fonction',
        resp_date_naissance: 'Date naissance', resp_nationalite: 'Nationalité',
        resp_numero_piece: 'N° pièce', resp_email: 'Email'
    };

    function getSummaryValue(id) {
        var el = document.getElementById(id);
        if (!el) return '';
        var v = el.value;
        if (id === 'type_institution') { var t = { association: 'Association', mutuelle: 'Mutuelle', cooperative: 'Coopérative', ong: 'ONG', fondation: 'Fondation', autre: 'Autre' }; return t[v] || v; }
        if (id === 'secteur') { var s = { microfinance: 'Microfinance', finance: 'Finance', agriculture: 'Agriculture', education: 'Éducation', sante: 'Santé', social: 'Social', environnement: 'Environnement', autre: 'Autre' }; return s[v] || v; }
        if (id === 'pays_id' || id === 'departement_id' || id === 'commune_id' || id === 'arrondissement_id' || id === 'quartier_id') {
            var sel = el.options[el.selectedIndex]; return sel ? sel.text : v;
        }
        return v;
    }

    function buildSummary() {
        var generalFields = ['nom_officiel', 'sigle', 'type_institution', 'rccm', 'ifu', 'date_creation', 'agrement', 'secteur'];
        var gh = '';
        generalFields.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.value.trim()) {
                gh += '<div class="summary-item"><div class="summary-label">' + (SUMMARY_LABELS[id] || id) + '</div><div class="summary-value">' + getSummaryValue(id) + '</div></div>';
            }
        });
        document.getElementById('summaryGeneral').innerHTML = gh;

        var locFields = ['pays_id', 'departement_id', 'commune_id', 'adresse'];
        var lh = '';
        locFields.forEach(function(id) {
            var el = document.getElementById(id);
            var v = '';
            if (id === 'arrondissement_id') {
                var t = document.getElementById('arrondissement_nom');
                v = (t && !t.disabled) ? t.value : (el && el.value ? getSummaryValue(id) : '');
            } else if (id === 'quartier_id') {
                var t = document.getElementById('quartier_nom');
                v = (t && !t.disabled) ? t.value : (el && el.value ? getSummaryValue(id) : '');
            } else {
                v = (el && el.value) ? getSummaryValue(id) : '';
            }
            if (v) lh += '<div class="summary-item"><div class="summary-label">' + (SUMMARY_LABELS[id] || id) + '</div><div class="summary-value">' + v + '</div></div>';
        });
        document.getElementById('summaryLocation').innerHTML = lh;

        var respFields = ['resp_prenom', 'resp_nom', 'resp_fonction', 'resp_date_naissance', 'resp_nationalite', 'resp_numero_piece', 'resp_email'];
        var rh = '';
        respFields.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.value.trim()) {
                rh += '<div class="summary-item"><div class="summary-label">' + (SUMMARY_LABELS[id] || id) + '</div><div class="summary-value">' + el.value + '</div></div>';
            }
        });
        document.getElementById('summaryResponsable').innerHTML = rh;
    }

    // Step 7 checkboxes
    function updateSubmitButton() {
        var btn = document.getElementById('register-submit');
        if (!btn) return;
        btn.disabled = !(document.getElementById('accept_cgu').checked && document.getElementById('accept_confidentialite').checked && document.getElementById('confirm_exactitude').checked);
    }
    document.getElementById('accept_cgu').addEventListener('change', updateSubmitButton);
    document.getElementById('accept_confidentialite').addEventListener('change', updateSubmitButton);
    document.getElementById('confirm_exactitude').addEventListener('change', updateSubmitButton);

    // ===================== FORM SUBMIT =====================
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (!validateStep(7)) { e.preventDefault(); goToStep(7); return; }
        var full3 = getFullPhone3();
        if (full3 && phoneInput3) phoneInput3.value = full3;
        var full4 = getFullPhone4();
        if (full4 && phoneInput4) phoneInput4.value = full4;
        var btn = document.getElementById('register-submit');
        btn.classList.add('loading');
        btn.disabled = true;
    });

    // ===================== TOAST =====================
    function showToast(message, type) {
        var existing = document.querySelector('.toast-notification');
        if (existing) existing.remove();
        var toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1a1a2e;color:#fff;padding:14px 24px;border-radius:14px;font-size:14px;z-index:9999;box-shadow:0 8px 32px rgba(0,0,0,0.2);max-width:90%;text-align:center;animation:toastIn 0.3s ease;' + (type === 'error' ? 'background:#dc2626;' : '') + 'font-weight:500;';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(function() { toast.remove(); }, 300);
        }, 4000);
    }
    if (!document.getElementById('toastStyles')) {
        var s = document.createElement('style');
        s.id = 'toastStyles';
        s.textContent = '@keyframes toastIn { from { opacity:0; transform:translateX(-50%) translateY(20px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }';
        document.head.appendChild(s);
    }

    document.querySelectorAll('.input-group-custom input, .input-group-custom select, .input-group-custom textarea').forEach(function(el) {
        el.addEventListener('input', function() { this.closest('.input-group-custom')?.classList.remove('is-invalid'); });
        el.addEventListener('change', function() { this.closest('.input-group-custom')?.classList.remove('is-invalid'); });
    });

    // ===================== LOCATION CASCADE =====================
    var API_BASE = '/api/v1/locations';
    var STEP2_FIELDS = ['pays_id', 'departement_id', 'commune_id', 'arrondissement_id', 'quartier_id', 'adresse'];

    function resetCascade(fromId) {
        var cascade = ['departement_id', 'commune_id', 'arrondissement_id', 'quartier_id'];
        var idx = cascade.indexOf(fromId);
        if (idx === -1) idx = 0;
        for (var i = idx; i < cascade.length; i++) {
            var el = document.getElementById(cascade[i]);
            if (!el) continue;
            if (cascade[i] === 'quartier_id') { quartierResetToDisabled(); continue; }
            el.innerHTML = '<option value="">' + (el.getAttribute('data-placeholder') || 'Chargement...') + '</option>';
            el.disabled = true;
            el.style.display = 'block';
            if (cascade[i] === 'arrondissement_id') {
                var textEl = document.getElementById('arrondissement_nom');
                if (textEl) { textEl.value = ''; textEl.style.display = 'none'; textEl.disabled = true; }
                var btn = document.getElementById('toggleArrondissement');
                if (btn) { btn.setAttribute('data-mode', 'select'); btn.innerHTML = 'Je ne trouve pas mon arrondissement <i class="fe fe-edit"></i>'; btn.style.display = 'none'; }
            }
        }
    }

    function loadLocation(url, selectEl, placeholder) {
        var spinner = document.getElementById(selectEl.id + 'Spinner');
        if (spinner) spinner.style.display = 'flex';
        selectEl.value = '';
        selectEl.disabled = true;
        if (!url) {
            selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
            if (spinner) spinner.style.display = 'none';
            validateStep2Field(selectEl.id);
            return;
        }
        fetch(url)
            .then(function(r) { if (!r.ok) throw new Error('Erreur reseau (' + r.status + ')'); return r.json(); })
            .then(function(data) {
                selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
                if (data && data.length > 0) {
                    data.forEach(function(item) {
                        var opt = document.createElement('option');
                        opt.value = item.id; opt.textContent = item.nom;
                        selectEl.appendChild(opt);
                    });
                }
                selectEl.disabled = false;
                if (spinner) spinner.style.display = 'none';
                validateStep2Field(selectEl.id);
                var toggleId = selectEl.id === 'arrondissement_id' ? 'toggleArrondissement' : (selectEl.id === 'quartier_id' ? 'toggleQuartier' : null);
                if (toggleId) { var btn = document.getElementById(toggleId); if (btn) btn.style.display = 'inline-flex'; }
            })
            .catch(function(err) {
                selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
                selectEl.disabled = false;
                if (spinner) spinner.style.display = 'none';
                validateStep2Field(selectEl.id);
            });
    }

    function toggleFieldMode(field) {
        var cfg = { arrondissement: { selectId: 'arrondissement_id', textId: 'arrondissement_nom', toggleId: 'toggleArrondissement' }, quartier: { selectId: 'quartier_id', textId: 'quartier_nom', toggleId: 'toggleQuartier' } }[field];
        if (!cfg) return;
        var select = document.getElementById(cfg.selectId), text = document.getElementById(cfg.textId), btn = document.getElementById(cfg.toggleId);
        if (!select || !text || !btn) return;
        if (btn.getAttribute('data-mode') === 'select') {
            select.style.display = 'none'; select.disabled = true;
            text.style.display = 'block'; text.disabled = false; text.focus();
            btn.setAttribute('data-mode', 'text');
            btn.innerHTML = '<i class="fe fe-list"></i> Choisir dans la liste';
            if (field === 'arrondissement') {
                var qSelect = document.getElementById('quartier_id'), qText = document.getElementById('quartier_nom'), qBtn = document.getElementById('toggleQuartier');
                if (qSelect && qBtn) { qSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>'; qSelect.disabled = true; qSelect.style.display = 'none'; qText.style.display = 'block'; qText.disabled = false; qText.value = ''; qBtn.setAttribute('data-mode', 'text'); qBtn.innerHTML = '<i class="fe fe-list"></i> Choisir dans la liste'; qBtn.style.display = 'inline-flex'; showFieldError('quartier_id', ''); }
            }
        } else {
            select.style.display = 'block'; select.disabled = false;
            text.style.display = 'none'; text.disabled = true;
            btn.setAttribute('data-mode', 'select');
            btn.innerHTML = 'Je ne trouve pas mon ' + field + ' <i class="fe fe-edit"></i>';
            if (field === 'arrondissement') { quartierResetToDisabled(); if (select.value) { var q = document.getElementById('quartier_id'); if (q) loadLocation(API_BASE + '/arrondissements/' + select.value + '/quartiers', q, 'Sélectionnez un quartier'); } }
        }
        validateStep2Field(cfg.selectId);
        updateStep2Button();
    }

    function quartierResetToDisabled() {
        var qSelect = document.getElementById('quartier_id'), qText = document.getElementById('quartier_nom'), qBtn = document.getElementById('toggleQuartier');
        if (qSelect) { qSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>'; qSelect.disabled = true; qSelect.style.display = 'block'; qSelect.value = ''; }
        if (qText) { qText.value = ''; qText.style.display = 'none'; qText.disabled = true; }
        if (qBtn) { qBtn.setAttribute('data-mode', 'select'); qBtn.innerHTML = 'Je ne trouve pas mon quartier <i class="fe fe-edit"></i>'; qBtn.style.display = 'none'; }
        showFieldError('quartier_id', ''); showFieldError('quartier_nom', '');
    }

    document.getElementById('toggleArrondissement').addEventListener('click', function(e) { e.preventDefault(); toggleFieldMode('arrondissement'); });
    document.getElementById('toggleQuartier').addEventListener('click', function(e) { e.preventDefault(); toggleFieldMode('quartier'); });

    function getFieldValue(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }
    function isFieldValid(id) { var v = getFieldValue(id); return v !== '' && v !== null; }

    function showFieldError(id, message) {
        var group = document.getElementById(id)?.closest('.input-group-custom');
        var errorEl = document.getElementById(id + 'Error');
        if (group) { if (message) group.classList.add('is-invalid'); else group.classList.remove('is-invalid'); }
        if (errorEl) { if (message) { errorEl.textContent = message; errorEl.classList.add('visible'); errorEl.style.display = 'flex'; } else { errorEl.textContent = ''; errorEl.classList.remove('visible'); errorEl.style.display = 'none'; } }
    }

    function validateStep2Field(fieldId) {
        var el = document.getElementById(fieldId);
        var textId = null;
        if (fieldId === 'arrondissement_id') textId = 'arrondissement_nom';
        else if (fieldId === 'quartier_id') textId = 'quartier_nom';
        if (textId) {
            var textEl = document.getElementById(textId);
            if (textEl && !textEl.disabled) { if (!textEl.value.trim() || textEl.value.trim().length < 2) { showFieldError(fieldId, 'Veuillez saisir ce champ (min. 2 caractères)'); return false; } showFieldError(fieldId, ''); return true; }
        }
        if (!el || el.disabled) { showFieldError(fieldId, ''); return true; }
        var val = getFieldValue(fieldId);
        if (fieldId === 'adresse') { if (val.length < 3) { showFieldError(fieldId, 'Adresse requise (min. 3 caractères)'); return false; } showFieldError(fieldId, ''); return true; }
        if (!val) { var labels = { pays_id: 'Veuillez sélectionner un pays', departement_id: 'Veuillez sélectionner un département', commune_id: 'Veuillez sélectionner une commune', arrondissement_id: 'Veuillez sélectionner un arrondissement', quartier_id: 'Veuillez sélectionner un quartier' }; showFieldError(fieldId, labels[fieldId] || 'Ce champ est requis'); return false; }
        showFieldError(fieldId, ''); return true;
    }

    function isStep2Valid() {
        for (var i = 0; i < STEP2_FIELDS.length; i++) {
            var id = STEP2_FIELDS[i], el = document.getElementById(id);
            if (!el) continue;
            if (id === 'arrondissement_id' || id === 'quartier_id') {
                var textId = id === 'arrondissement_id' ? 'arrondissement_nom' : 'quartier_nom', textEl = document.getElementById(textId);
                if (textEl && !textEl.disabled) { if (textEl.value.trim().length < 2) return false; continue; }
            }
            if (el.disabled) return false;
            if (!isFieldValid(id)) return false;
            if (id === 'adresse' && getFieldValue(id).length < 3) return false;
        }
        return true;
    }

    function updateStep2Button() { var btn = document.getElementById('step2Next'); if (btn) btn.disabled = !isStep2Valid(); }
    function revalidateStep2() { for (var i = 0; i < STEP2_FIELDS.length; i++) validateStep2Field(STEP2_FIELDS[i]); updateStep2Button(); }

    function setupStep2Listeners() {
        for (var i = 0; i < STEP2_FIELDS.length; i++) {
            var el = document.getElementById(STEP2_FIELDS[i]);
            if (!el) continue;
            el.addEventListener('change', function() { validateStep2Field(this.id); updateStep2Button(); });
            el.addEventListener('input', function() { validateStep2Field(this.id); updateStep2Button(); });
        }
        ['arrondissement_nom', 'quartier_nom'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', function() { var s = this.id === 'arrondissement_nom' ? 'arrondissement_id' : 'quartier_id'; validateStep2Field(s); updateStep2Button(); });
        });
    }

    document.querySelectorAll('.location-select').forEach(function(el) {
        var firstOpt = el.querySelector('option');
        if (firstOpt) el.setAttribute('data-placeholder', firstOpt.textContent);
    });
    setupStep2Listeners();
    updateStep2Button();

    document.getElementById('step2Next').addEventListener('click', function() {
        revalidateStep2();
        if (isStep2Valid()) goToStep(3);
        else showToast('Veuillez remplir tous les champs obligatoires de la localisation.', 'error');
    });

    // ==================== LOCATION EVENTS ====================
    var paysSelect = document.getElementById('pays_id');
    if (paysSelect) {
        loadLocation(API_BASE + '/pays', paysSelect, 'Sélectionnez votre pays');
        paysSelect.addEventListener('change', function() {
            var payId = this.value;
            resetCascade('departement_id');
            var depSelect = document.getElementById('departement_id');
            if (payId) loadLocation(API_BASE + '/pays/' + payId + '/departements', depSelect, 'Sélectionnez le département');
            else { depSelect.innerHTML = '<option value="">Sélectionnez un pays d\'abord</option>'; depSelect.disabled = true; revalidateStep2(); }
        });
    }

    var depSelect = document.getElementById('departement_id');
    if (depSelect) {
        depSelect.addEventListener('change', function() {
            var depId = this.value;
            resetCascade('commune_id');
            var comSelect = document.getElementById('commune_id');
            if (depId) loadLocation(API_BASE + '/departements/' + depId + '/communes', comSelect, 'Sélectionnez la commune');
            else { comSelect.innerHTML = '<option value="">Sélectionnez un département d\'abord</option>'; comSelect.disabled = true; revalidateStep2(); }
        });
    }

    var comSelect = document.getElementById('commune_id');
    if (comSelect) {
        comSelect.addEventListener('change', function() {
            var comId = this.value;
            resetCascade('arrondissement_id');
            var arrSelect = document.getElementById('arrondissement_id');
            if (comId) loadLocation(API_BASE + '/communes/' + comId + '/arrondissements', arrSelect, "Sélectionnez l'arrondissement");
            else { arrSelect.innerHTML = '<option value="">Sélectionnez une commune d\'abord</option>'; arrSelect.disabled = true; revalidateStep2(); }
        });
    }

    var arrSelect = document.getElementById('arrondissement_id');
    if (arrSelect) {
        arrSelect.addEventListener('change', function() {
            var arrId = this.value;
            resetCascade('quartier_id');
            var quarSelect = document.getElementById('quartier_id');
            if (arrId) loadLocation(API_BASE + '/arrondissements/' + arrId + '/quartiers', quarSelect, 'Sélectionnez le quartier');
            else { quarSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>'; quarSelect.disabled = true; revalidateStep2(); }
        });
    }

    // Init
    updateStep1Button();
    updateStep6Button();
})();
</script>
@endpush

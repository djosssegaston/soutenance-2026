@extends('layouts.auth')

@push('meta')
<meta name="description" content="Alogoto – Inscription">
<meta name="keywords" content="microfinance, inscription, Alogoto">
@endpush

@push('title')
Inscription
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
    font-size: 11px;
    color: #6c757d;
    text-align: center;
    margin-top: 6px;
    font-weight: 500;
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
}

.btn-prev:hover {
    border-color: var(--login-accent);
    color: var(--login-accent);
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
}

.btn-next:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 32px rgba(252, 160, 40, 0.4);
}

.btn-next:disabled, .btn-prev:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.form-row-3 {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.form-row-3 .form-group {
    flex: 1;
    min-width: 120px;
}

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

.upload-zone {
    border: 2px dashed #e9ecef;
    border-radius: var(--login-input-radius);
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafbfc;
    position: relative;
    min-height: 120px;
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

.upload-error {
    font-size: 12px;
    color: #dc3545;
    margin-top: 4px;
    display: none;
}

.upload-error.visible {
    display: block;
}

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

select.location-select {
    min-width: 0;
    width: 100%;
}

.form-group select.location-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 10px;
    padding-right: 32px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}

select.location-select:disabled {
    background-color: #f0f1f3;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.7;
}

select.location-select optgroup, select.location-select option {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
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

.form-group select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 10px;
    padding-right: 32px;
}

.required-star { color: #dc3545; margin-left: 2px; }

.field-error {
    font-size: 12px;
    color: #dc3545;
    margin-top: 4px;
    padding-left: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.field-error.visible {
    display: flex !important;
}

select.location-select.is-invalid,
input.form-control-custom.is-invalid {
    color: #dc3545;
}

/* Porteur type toggle */
.porteur-type-toggle {
    display: flex;
    gap: 0;
    margin-bottom: 24px;
    background: #f0f1f3;
    border-radius: 12px;
    padding: 4px;
}

.porteur-type-btn {
    flex: 1;
    padding: 12px 16px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #6c757d;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.porteur-type-btn i {
    font-size: 18px;
}

.porteur-type-btn.active {
    background: #fff;
    color: #1a1a2e;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.porteur-type-btn:hover:not(.active) {
    color: #495057;
}

.porteur-type-fields {
    display: block;
}

.porteur-type-fields.hidden {
    display: none;
}

@media (max-width: 576px) {
    .radio-cards { flex-direction: column; }
    .radio-card { min-width: unset; }
    .form-row-3 { flex-direction: column; }
    .form-row-3 .form-group { min-width: unset; }
    .nav-buttons { flex-direction: column; }
    .upload-zone { min-height: 100px; padding: 16px 12px; }
    .upload-zone .upload-icon { font-size: 24px; }
    .porteur-type-btn { font-size: 13px; padding: 10px 12px; }
    .porteur-type-btn i { font-size: 16px; }
}

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
.input-group-custom:has(.phone-prefix-btn) input#telephone { padding-left: 4px; }
input#telephone:disabled { background: transparent; color: #adb5bd; cursor: default; }
@media (max-width: 576px) { .phone-prefix-dropdown { min-width: 200px; max-height: 200px; } .phone-prefix-btn { padding: 12px 6px; font-size: 13px; } }

.toggle-field-btn {
    background: none;
    border: none;
    color: var(--login-accent, #f6a623);
    font-size: 13px;
    cursor: pointer;
    padding: 4px 0 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: underline;
    text-underline-offset: 2px;
}
.toggle-field-btn:hover { color: #d4891a; }
.toggle-field-btn .fe-edit { font-size: 11px; }

.input-group-custom .form-control-custom[style*="display: block"] + .input-icon { display: none; }
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
        <h2>Créer un compte</h2>
        <p>Rejoignez Alogoto et donnez vie à vos projets</p>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <i class="fe fe-alert-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if (session('status'))
        <div class="alert-success">
            <i class="fe fe-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Porteur Type Toggle -->
    <div class="porteur-type-toggle" id="porteurTypeToggle">
        <button type="button" class="porteur-type-btn active" data-type="personnel">
            <i class="fe fe-user"></i> Personnel
        </button>
        <button type="button" class="porteur-type-btn" data-type="entreprise">
            <i class="fe fe-briefcase"></i> Entreprise
        </button>
    </div>

    <!-- Progress Bar -->
    <div class="steps-progress" id="progressBar">
        <div class="step-indicator">
            <div class="step-dot active" data-step="1">1</div>
            <div class="step-label">Compte</div>
        </div>
        <div class="step-connector" data-connector="1"></div>
        <div class="step-indicator">
            <div class="step-dot" data-step="2">2</div>
            <div class="step-label">Localisation</div>
        </div>
        <div class="step-connector" data-connector="2"></div>
        <div class="step-indicator">
            <div class="step-dot" data-step="3">3</div>
            <div class="step-label">Identité</div>
        </div>
    </div>

    <form method="POST" action="{{ route('register.perform') }}" id="registerForm" novalidate enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="role" value="porteur">
        <input type="hidden" name="porteur_type" id="porteurTypeInput" value="{{ old('porteur_type', 'personnel') }}">

        <!-- ============================================================ -->
        <!-- STEP 1: CREATION DU COMPTE -->
        <!-- ============================================================ -->
        <div class="step-content active" data-step="1">
            <div class="step-title">Création du compte</div>
            <div class="step-subtitle" id="step1Subtitle">Informations personnelles de base</div>

            <!-- Personnel fields -->
            <div class="porteur-type-fields" id="personnelFields">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">Prénom <span class="required-star">*</span></label>
                        <div class="input-group-custom @error('prenom') is-invalid @enderror">
                            <span class="input-icon"><i class="fe fe-user"></i></span>
                            <input type="text" id="prenom" name="prenom" class="form-control-custom" placeholder="Votre prénom" value="{{ old('prenom') }}" required autofocus>
                        </div>
                        @error('prenom')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Nom <span class="required-star">*</span></label>
                        <div class="input-group-custom @error('name') is-invalid @enderror">
                            <span class="input-icon"><i class="fe fe-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control-custom" placeholder="Votre nom" value="{{ old('name') }}" required>
                        </div>
                        @error('name')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sexe">Sexe</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fe fe-users"></i></span>
                            <select id="sexe" name="sexe">
                                <option value="">Sélectionner</option>
                                <option value="homme" {{ old('sexe') === 'homme' ? 'selected' : '' }}>Homme</option>
                                <option value="femme" {{ old('sexe') === 'femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date_naissance">Date de naissance</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fe fe-calendar"></i></span>
                            <input type="date" id="date_naissance" name="date_naissance" class="form-control-custom" value="{{ old('date_naissance') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entreprise fields -->
            <div class="porteur-type-fields hidden" id="entrepriseFields">
                <div class="form-group">
                    <label for="entreprise_nom">Nom de l'entreprise <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('entreprise_nom') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-briefcase"></i></span>
                        <input type="text" id="entreprise_nom" name="entreprise_nom" class="form-control-custom" placeholder="Raison sociale" value="{{ old('entreprise_nom') }}">
                    </div>
                    @error('entreprise_nom')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="entreprise_secteur">Secteur d'activité <span class="required-star">*</span></label>
                        <div class="input-group-custom @error('entreprise_secteur') is-invalid @enderror">
                            <span class="input-icon"><i class="fe fe-grid"></i></span>
                            <select id="entreprise_secteur" name="entreprise_secteur">
                                <option value="">Sélectionner</option>
                                <option value="agriculture" {{ old('entreprise_secteur') === 'agriculture' ? 'selected' : '' }}>Agriculture</option>
                                <option value="elevage" {{ old('entreprise_secteur') === 'elevage' ? 'selected' : '' }}>Élevage</option>
                                <option value="commerce" {{ old('entreprise_secteur') === 'commerce' ? 'selected' : '' }}>Commerce</option>
                                <option value="artisanat" {{ old('entreprise_secteur') === 'artisanat' ? 'selected' : '' }}>Artisanat</option>
                                <option value="technologie" {{ old('entreprise_secteur') === 'technologie' ? 'selected' : '' }}>Technologie</option>
                                <option value="transport" {{ old('entreprise_secteur') === 'transport' ? 'selected' : '' }}>Transport</option>
                                <option value="immobilier" {{ old('entreprise_secteur') === 'immobilier' ? 'selected' : '' }}>Immobilier</option>
                                <option value="restauration" {{ old('entreprise_secteur') === 'restauration' ? 'selected' : '' }}>Restauration</option>
                                <option value="sante" {{ old('entreprise_secteur') === 'sante' ? 'selected' : '' }}>Santé</option>
                                <option value="education" {{ old('entreprise_secteur') === 'education' ? 'selected' : '' }}>Éducation</option>
                                <option value="autre" {{ old('entreprise_secteur') === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        @error('entreprise_secteur')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label for="activite">Activité principale <span class="required-star">*</span></label>
                        <div class="input-group-custom @error('activite') is-invalid @enderror">
                            <span class="input-icon"><i class="fe fe-activity"></i></span>
                            <input type="text" id="activite" name="activite" class="form-control-custom" placeholder="Décrivez l'activité" value="{{ old('activite') }}">
                        </div>
                        @error('activite')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="name_entreprise">Nom du contact <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('name') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-user"></i></span>
                        <input type="text" id="name_entreprise" name="name" class="form-control-custom" placeholder="Votre nom complet" value="{{ old('name') }}">
                    </div>
                    @error('name')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Common fields for both types -->
            <div class="form-group">
                <label for="email">Adresse e-mail <span class="required-star">*</span></label>
                <div class="input-group-custom @error('email') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-mail"></i></span>
                    <input type="email" id="email" name="email" class="form-control-custom" placeholder="exemple@email.com" value="{{ old('email') }}" required>
                </div>
                @error('email')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone <span class="required-star">*</span></label>
                <div class="input-group-custom @error('telephone') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-phone"></i></span>
                    <div class="phone-prefix">
                        <button type="button" class="phone-prefix-btn" id="phonePrefixBtn" title="Choisir le pays">
                            <span class="phone-code" id="phoneCode">+229</span>
                            <i class="fe fe-chevron-down"></i>
                        </button>
                        <ul class="phone-prefix-dropdown" id="phonePrefixDropdown" role="listbox" aria-label="Indicatif téléphonique"></ul>
                    </div>
                    <input type="tel" id="telephone" name="telephone" class="form-control-custom" placeholder="Sélectionnez d'abord l'indicatif" value="{{ old('telephone') }}" disabled required>
                </div>
                <div class="field-error" id="telephoneError" style="display:none"></div>
                @error('telephone')<div class="field-error visible"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Mot de passe <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('password') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control-custom" placeholder="Minimum 8 caractères" required minlength="8">
                        <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Afficher le mot de passe">
                            <i class="fe fe-eye-off"></i>
                        </button>
                    </div>
                    @error('password')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <span data-index="0"></span>
                            <span data-index="1"></span>
                            <span data-index="2"></span>
                            <span data-index="3"></span>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control-custom" placeholder="Confirmez" required>
                    </div>
                    <div class="field-error" id="passwordMismatch" style="display:none"><i class="fe fe-alert-triangle"></i> Les mots de passe ne correspondent pas</div>
                </div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-next" onclick="goToStep(2)">Suivant <i class="fe fe-arrow-right ms-1"></i></button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- STEP 2: LOCALISATION -->
        <!-- ============================================================ -->
        <div class="step-content" data-step="2">
            <div class="step-title">Localisation</div>
            <div class="step-subtitle">Où êtes-vous situé ?</div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="pays_id">Pays <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map-pin"></i></span>
                        <select id="pays_id" name="pays_id" class="location-select" required>
                            <option value="">Chargement...</option>
                        </select>
                        <span class="input-spinner" id="paysSpinner" style="display:none"><i class="fe fe-loader fa-spin"></i></span>
                    </div>
                    <div class="field-error" id="paysError" style="display:none"></div>
                </div>

                <div class="form-group">
                    <label for="departement_id">Département <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map"></i></span>
                        <select id="departement_id" name="departement_id" class="location-select" required disabled>
                            <option value="">Sélectionnez un pays d'abord</option>
                        </select>
                        <span class="input-spinner" id="departementSpinner" style="display:none"><i class="fe fe-loader fa-spin"></i></span>
                    </div>
                    <div class="field-error" id="departementError" style="display:none"></div>
                </div>

                <div class="form-group">
                    <label for="commune_id">Commune <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map"></i></span>
                        <select id="commune_id" name="commune_id" class="location-select" required disabled>
                            <option value="">Sélectionnez un département d'abord</option>
                        </select>
                        <span class="input-spinner" id="communeSpinner" style="display:none"><i class="fe fe-loader fa-spin"></i></span>
                    </div>
                    <div class="field-error" id="communeError" style="display:none"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="arrondissement_id">Arrondissement <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map-pin"></i></span>
                        <select id="arrondissement_id" name="arrondissement_id" class="location-select" required disabled>
                            <option value="">Sélectionnez une commune d'abord</option>
                        </select>
                        <input type="text" id="arrondissement_nom" name="arrondissement_nom" class="form-control-custom" placeholder="Saisissez l'arrondissement" style="display:none" disabled>
                        <span class="input-spinner" id="arrondissementSpinner" style="display:none"><i class="fe fe-loader fa-spin"></i></span>
                    </div>
                    <div class="field-error" id="arrondissementError" style="display:none"></div>
                    <button type="button" class="toggle-field-btn" id="toggleArrondissement" data-mode="select" style="display:none">Je ne trouve pas mon arrondissement <i class="fe fe-edit"></i></button>
                </div>

                <div class="form-group">
                    <label for="quartier_id">Quartier <span class="required-star">*</span></label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="fe fe-map-pin"></i></span>
                        <select id="quartier_id" name="quartier_id" class="location-select" required disabled>
                            <option value="">Sélectionnez un arrondissement d'abord</option>
                        </select>
                        <input type="text" id="quartier_nom" name="quartier_nom" class="form-control-custom" placeholder="Saisissez le quartier" style="display:none" disabled>
                        <span class="input-spinner" id="quartierSpinner" style="display:none"><i class="fe fe-loader fa-spin"></i></span>
                    </div>
                    <div class="field-error" id="quartierError" style="display:none"></div>
                    <button type="button" class="toggle-field-btn" id="toggleQuartier" data-mode="select" style="display:none">Je ne trouve pas mon quartier <i class="fe fe-edit"></i></button>
                </div>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse complète <span class="required-star">*</span></label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="fe fe-home"></i></span>
                    <input type="text" id="adresse" name="adresse" class="form-control-custom" placeholder="Numéro, rue, porte..." value="{{ old('adresse') }}" required>
                </div>
                <div class="field-error" id="adresseError" style="display:none"></div>
            </div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(1)"><i class="fe fe-arrow-left me-1"></i> Retour</button>
                <button type="button" class="btn-next" id="step2Next" disabled>Suivant <i class="fe fe-arrow-right ms-1"></i></button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- STEP 3: IDENTITE & DOCUMENTS -->
        <!-- ============================================================ -->
        <div class="step-content" data-step="3">
            <div class="step-title">Vérification d'identité</div>
            <div class="step-subtitle">Fournissez une pièce d'identité valide pour vérifier votre compte</div>

            <div class="form-group">
                <label>Type de pièce</label>
                <div class="radio-cards" id="typePieceGroup">
                    <div class="radio-card">
                        <input type="radio" name="type_piece" id="type_cni" value="cni" {{ old('type_piece') === 'cni' ? 'checked' : '' }}>
                        <label for="type_cni">
                            <i class="fe fe-credit-card"></i>
                            CNI
                        </label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" name="type_piece" id="type_passeport" value="passeport" {{ old('type_piece') === 'passeport' ? 'checked' : '' }}>
                        <label for="type_passeport">
                            <i class="fe fe-book"></i>
                            Passeport
                        </label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" name="type_piece" id="type_cip" value="cip" {{ old('type_piece') === 'cip' ? 'checked' : '' }}>
                        <label for="type_cip">
                            <i class="fe fe-file-text"></i>
                            CIP
                        </label>
                    </div>
                </div>
                @error('type_piece')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="numero_piece">Numéro de la pièce <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('numero_piece') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-hash"></i></span>
                        <input type="text" id="numero_piece" name="numero_piece" class="form-control-custom" placeholder="Numéro d'identité" value="{{ old('numero_piece') }}" required>
                    </div>
                    @error('numero_piece')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="date_expiration">Date d'expiration <span class="required-star">*</span></label>
                    <div class="input-group-custom @error('date_expiration') is-invalid @enderror">
                        <span class="input-icon"><i class="fe fe-calendar"></i></span>
                        <input type="date" id="date_expiration" name="date_expiration" class="form-control-custom" value="{{ old('date_expiration') }}" required>
                    </div>
                    <div class="field-error" id="dateExpirationError" style="display:none"><i class="fe fe-alert-triangle"></i> La pièce doit être en cours de validité</div>
                    @error('date_expiration')<div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>@enderror
                </div>
            </div>

            <div class="step-title" style="margin-top: 24px;">Documents requis</div>
            <div class="step-subtitle">Téléchargez les documents nécessaires selon votre profil</div>
            <div id="docs-container"></div>

            <div class="nav-buttons">
                <button type="button" class="btn-prev" onclick="goToStep(2)"><i class="fe fe-arrow-left me-1"></i> Retour</button>
                <button type="submit" class="btn-next" id="register-submit">
                    <span class="btn-text">Créer mon compte <i class="fe fe-check ms-1"></i></span>
                    <span class="spinner"></span>
                </button>
            </div>
        </div>
    </form>

    <div class="login-footer">
        <p>Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
    </div>
</div>
@endsection

@push('scripts')
@php
    use App\Models\Pay;
    use App\Models\DocumentRule;
    $phoneCountries = Pay::where('actif', true)->get(['id', 'nom', 'code', 'indicatif'])->toArray();
    $personnelDocRules = DocumentRule::active()->forContext('registration')->forRole('porteur')->ordered()->get(['slug', 'label', 'description', 'obligatoire', 'types_mime', 'max_size'])->toArray();
    $entrepriseDocRules = DocumentRule::active()->forContext('registration')->forRole('porteur_entreprise')->ordered()->get(['slug', 'label', 'description', 'obligatoire', 'types_mime', 'max_size'])->toArray();
@endphp
<script id="phoneCountriesData" type="application/json">@json($phoneCountries)</script>
<script id="personnelDocRulesData" type="application/json">@json($personnelDocRules)</script>
<script id="entrepriseDocRulesData" type="application/json">@json($entrepriseDocRules)</script>
<script>
(function() {
    'use strict';

    let currentStep = 1;
    const totalSteps = 3;
    let currentPorteurType = document.getElementById('porteurTypeInput')?.value || 'personnel';

    // ===================== PORTEUR TYPE TOGGLE =====================
    const typeBtns = document.querySelectorAll('.porteur-type-btn');
    const typeInput = document.getElementById('porteurTypeInput');
    const personnelFields = document.getElementById('personnelFields');
    const entrepriseFields = document.getElementById('entrepriseFields');
    const step1Subtitle = document.getElementById('step1Subtitle');

    function disableFields(container, disabled) {
        container.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.disabled = disabled;
        });
    }

    function setPorteurType(type) {
        currentPorteurType = type;
        typeInput.value = type;

        typeBtns.forEach(function(btn) {
            btn.classList.toggle('active', btn.dataset.type === type);
        });

        if (type === 'personnel') {
            personnelFields.classList.remove('hidden');
            entrepriseFields.classList.add('hidden');
            disableFields(personnelFields, false);
            disableFields(entrepriseFields, true);
            step1Subtitle.textContent = 'Informations personnelles de base';
            document.getElementById('prenom').setAttribute('required', '');
            document.getElementById('name').setAttribute('required', '');
            document.getElementById('entreprise_nom')?.removeAttribute('required');
            document.getElementById('entreprise_secteur')?.removeAttribute('required');
            document.getElementById('activite')?.removeAttribute('required');
        } else {
            personnelFields.classList.add('hidden');
            entrepriseFields.classList.remove('hidden');
            disableFields(personnelFields, true);
            disableFields(entrepriseFields, false);
            step1Subtitle.textContent = 'Informations de l\'entreprise';
            document.getElementById('prenom')?.removeAttribute('required');
            document.getElementById('name')?.removeAttribute('required');
            document.getElementById('entreprise_nom')?.setAttribute('required', '');
            document.getElementById('entreprise_secteur')?.setAttribute('required', '');
            document.getElementById('activite')?.setAttribute('required', '');
        }

        resetStep1Errors();
        updateStep1Button();
        initDocUploads();
    }

    typeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (currentStep !== 1) {
                showToast('Veuillez finaliser l\'étape en cours avant de changer de profil.', 'error');
                return;
            }
            setPorteurType(this.dataset.type);
        });
    });

    setPorteurType(currentPorteurType);

    function resetStep1Errors() {
        document.querySelectorAll('.step-content[data-step="1"] .input-group-custom.is-invalid').forEach(function(el) {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.step-content[data-step="1"] .field-error.visible').forEach(function(el) {
            el.classList.remove('visible');
            el.style.display = 'none';
        });
    }

    // ===================== STEP NAVIGATION =====================
    window.goToStep = function(step) {
        if (step < 1 || step > totalSteps) return;
        if (step > currentStep) {
            if (!validateStep(currentStep)) return;
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
            dot.classList.toggle('active', s === currentStep);
            dot.classList.toggle('completed', s < currentStep);
        });

        document.querySelectorAll('.step-connector').forEach(function(conn) {
            var s = parseInt(conn.dataset.connector);
            conn.classList.toggle('completed', s < currentStep);
        });

        document.getElementById('registerForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // ===================== STEP VALIDATION =====================
    function validateStep(step) {
        var container = document.querySelector('.step-content[data-step="' + step + '"]');
        if (!container) return true;

        var inputs = container.querySelectorAll('input[required], select[required]');
        var valid = true;

        inputs.forEach(function(input) {
            if (input.disabled) return;
            if (!input.value || input.value.trim() === '') {
                var group = input.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                valid = false;
            } else {
                var group = input.closest('.input-group-custom');
                if (group) group.classList.remove('is-invalid');
            }

            if (input.type === 'email' && input.value) {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(input.value)) {
                    var group = input.closest('.input-group-custom');
                    if (group) group.classList.add('is-invalid');
                    valid = false;
                }
            }

            if (input.type === 'tel' && input.value) {
                var digits = input.value.replace(/\D/g, '');
                if (digits.length < 6) {
                    var group = input.closest('.input-group-custom');
                    if (group) group.classList.add('is-invalid');
                    valid = false;
                }
            }
        });

        if (step === 1) {
            var pw = document.getElementById('password');
            var pw2 = document.getElementById('password_confirmation');
            var mismatchEl = document.getElementById('passwordMismatch');
            if (pw.value && pw2.value && pw.value !== pw2.value) {
                mismatchEl.style.display = 'flex';
                var group = pw.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                group = pw2.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                valid = false;
            } else {
                mismatchEl.style.display = 'none';
            }

            if (pw.value.length > 0 && pw.value.length < 8) {
                var group = pw.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                valid = false;
            }

            var email = document.getElementById('email');
            var tel = document.getElementById('telephone');
            if (!email.value) { var group = email.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
            if (!tel.value || tel.disabled || tel.value.replace(/\D/g, '').length < 6) {
                var group = tel.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                valid = false;
            }

            // Personnel-specific required fields
            if (currentPorteurType === 'personnel') {
                var prenom = document.getElementById('prenom');
                var name = document.getElementById('name');
                if (!prenom.value) { var group = prenom.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
                if (!name.value) { var group = name.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
            }

            // Entreprise-specific required fields
            if (currentPorteurType === 'entreprise') {
                var entNom = document.getElementById('entreprise_nom');
                var entSect = document.getElementById('entreprise_secteur');
                var act = document.getElementById('activite');
                var entName = document.getElementById('name_entreprise');
                if (!entNom.value) { var group = entNom.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
                if (!entSect.value) { var group = entSect.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
                if (!act.value) { var group = act.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
                if (!entName.value) { var group = entName.closest('.input-group-custom'); if (group) group.classList.add('is-invalid'); valid = false; }
            }
        }

        if (step === 2) {
            var pays = document.getElementById('pays_id');
            if (pays && !pays.value) {
                var group = pays.closest('.input-group-custom');
                if (group) group.classList.add('is-invalid');
                valid = false;
            }
        }

        if (step === 3) {
            var typePiece = document.querySelector('input[name="type_piece"]:checked');
            if (!typePiece) {
                document.getElementById('typePieceGroup')?.classList.add('is-invalid');
                valid = false;
            }

            // Validate dynamic document uploads
            var rules = getCurrentDocRules();
            rules.forEach(function(rule) {
                var input = document.getElementById(rule.slug);
                if (rule.obligatoire !== false && (!input || !input.files || !input.files.length)) {
                    valid = false;
                }
            });

            var expDate = document.getElementById('date_expiration');
            var expError = document.getElementById('dateExpirationError');
            if (expDate && expDate.value) {
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                var exp = new Date(expDate.value + 'T00:00:00');
                if (exp <= today) {
                    var group = expDate.closest('.input-group-custom');
                    if (group) group.classList.add('is-invalid');
                    if (expError) { expError.style.display = 'flex'; expError.classList.add('visible'); }
                    valid = false;
                } else {
                    var group = expDate.closest('.input-group-custom');
                    if (group) group.classList.remove('is-invalid');
                    if (expError) { expError.style.display = 'none'; expError.classList.remove('visible'); }
                }
            }
        }

        if (!valid) {
            showToast('Veuillez remplir tous les champs obligatoires.', 'error');
        }

        return valid;
    }

    // ===================== PASSWORD STRENGTH =====================
    var pwInput = document.getElementById('password');
    var strengthEl = document.getElementById('passwordStrength');
    var strengthText = document.getElementById('strengthText');
    var bars = strengthEl.querySelectorAll('.strength-bar span');

    pwInput.addEventListener('input', function() {
        var val = this.value;
        if (val.length === 0) {
            strengthEl.classList.remove('visible');
            return;
        }
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
    });

    // ===================== PASSWORD TOGGLE =====================
    document.getElementById('togglePassword').addEventListener('click', function() {
        var pw = document.getElementById('password');
        var icon = this.querySelector('i');
        if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fe fe-eye'; }
        else { pw.type = 'password'; icon.className = 'fe fe-eye-off'; }
    });

    // ===================== STEP 1 FIELD VALIDATION =====================
    var STEP1_INPUTS = ['prenom', 'name', 'email', 'password', 'password_confirmation', 'entreprise_nom', 'entreprise_secteur', 'activite', 'name_entreprise'];
    for (var i = 0; i < STEP1_INPUTS.length; i++) {
        var el = document.getElementById(STEP1_INPUTS[i]);
        if (el) {
            el.addEventListener('input', updateStep1Button);
        }
    }

    // ===================== UNIQUENESS CHECK (AJAX) =====================
    var UNIQUE_FIELDS = ['email', 'name', 'prenom'];
    var uniquenessTimers = {};

    function checkUniqueness(field, value) {
        var el = document.getElementById(field);
        var group = el ? el.closest('.input-group-custom') : null;
        var errorEl = document.getElementById(field + 'UniqueError');
        if (!errorEl && el && el.parentNode) {
            errorEl = document.createElement('div');
            errorEl.className = 'field-error';
            errorEl.id = field + 'UniqueError';
            errorEl.style.display = 'none';
            el.parentNode.appendChild(errorEl);
        }

        if (!value || value.length < 2) {
            if (group) group.classList.remove('is-invalid');
            if (errorEl) { errorEl.textContent = ''; errorEl.classList.remove('visible'); errorEl.style.display = 'none'; }
            return;
        }

        var url = '/api/v1/check-uniqueness?field=' + encodeURIComponent(field) + '&value=' + encodeURIComponent(value);
        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.exists) {
                    if (group) group.classList.add('is-invalid');
                    if (errorEl) {
                        var labels = { email: 'cet email', name: 'ce nom', prenom: 'ce prénom' };
                        errorEl.textContent = labels[field] || 'Cette valeur';
                        errorEl.textContent += ' est déjà utilisé';
                        errorEl.classList.add('visible');
                        errorEl.style.display = 'flex';
                    }
                } else {
                    if (group) group.classList.remove('is-invalid');
                    if (errorEl) { errorEl.textContent = ''; errorEl.classList.remove('visible'); errorEl.style.display = 'none'; }
                }
            })
            .catch(function() {});
    }

    for (var u = 0; u < UNIQUE_FIELDS.length; u++) {
        (function(field) {
            var el = document.getElementById(field);
            if (!el) return;
            el.addEventListener('blur', function() {
                checkUniqueness(field, this.value.trim());
            });
            el.addEventListener('input', function() {
                var val = this.value.trim();
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

    // ===================== PASSWORD CONFIRMATION MATCH =====================
    document.getElementById('password_confirmation').addEventListener('input', function() {
        var pw = document.getElementById('password').value;
        var mismatchEl = document.getElementById('passwordMismatch');
        if (this.value && pw !== this.value) {
            mismatchEl.style.display = 'flex';
        } else {
            mismatchEl.style.display = 'none';
        }
        updateStep1Button();
    });

    // ===================== FILE UPLOAD HANDLER =====================
    function handleFileUpload(inputId, previewId, imgId, filenameId, errorId, zoneId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        var img = document.getElementById(imgId);
        var filename = document.getElementById(filenameId);
        var error = document.getElementById(errorId);
        var zone = document.getElementById(zoneId);

        input.addEventListener('change', function(e) {
            var file = this.files && this.files[0];

            if (!file) return;

            error.classList.remove('visible');

            var maxSizeMb = parseFloat(this.getAttribute('data-max-size')) || 5;
            var maxSizeBytes = maxSizeMb * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                error.textContent = 'Le fichier ne doit pas depasser ' + maxSizeMb + ' Mo.';
                error.classList.add('visible');
                this.value = '';
                return;
            }

            var typesAttr = this.getAttribute('data-types') || 'jpg,jpeg,png,pdf';
            var allowedMimeMap = { 'jpg': 'image/jpeg', 'jpeg': 'image/jpeg', 'png': 'image/png', 'gif': 'image/gif', 'webp': 'image/webp', 'pdf': 'application/pdf', 'doc': 'application/msword', 'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' };
            var allowedTypesList = typesAttr.split(',').map(function(t) { return allowedMimeMap[t.trim().toLowerCase()]; }).filter(Boolean);
            if (allowedTypesList.length && !allowedTypesList.includes(file.type)) {
                error.textContent = 'Format accepté: ' + typesAttr.toUpperCase() + '.';
                error.classList.add('visible');
                this.value = '';
                return;
            }

            filename.textContent = file.name;
            zone.classList.add('has-file');

            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.add('visible');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.remove('visible');
            }
        });

        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        zone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    // ===================== DYNAMIC DOCUMENT UPLOADS =====================
    function getCurrentDocRules() {
        var rules = [];
        try {
            var dataId = currentPorteurType === 'entreprise' ? 'entrepriseDocRulesData' : 'personnelDocRulesData';
            var scriptEl = document.getElementById(dataId);
            if (scriptEl) rules = JSON.parse(scriptEl.textContent);
        } catch(e) {}
        return rules;
    }

    function initDocUploads() {
        var rules = getCurrentDocRules();
        var container = document.getElementById('docs-container');
        if (!container) return;
        container.innerHTML = '';
        if (!rules.length) {
            container.innerHTML = '<p class="text-muted" style="font-size:13px;color:#6c757d;text-align:center;padding:16px;">Aucun document requis pour ce profil.</p>';
            return;
        }
        var currentRow = null;
        rules.forEach(function(rule, index) {
            var slug = rule.slug;
            var label = rule.label || slug;
            var obligatoire = rule.obligatoire !== false;
            var types = rule.types_mime || 'jpg,jpeg,png,pdf';
            var maxSize = rule.max_size || 5;
            var accept = '.' + types.replace(/,/g, ',.');
            var isImageOnly = types.split(',').every(function(t) { return t.trim().match(/^(jpg|jpeg|png|gif|webp)$/i); });
            var icon = isImageOnly ? 'fe fe-camera' : 'fe fe-upload';
            var hint = types.toUpperCase() + ' (max ' + maxSize + ' Mo)';

            var col = document.createElement('div');
            col.className = 'form-group';
            col.innerHTML =
                '<label>' + label + (obligatoire ? ' <span class="required-star">*</span>' : '') + '</label>' +
                '<div class="upload-zone" id="' + slug + 'Zone">' +
                    '<div class="upload-icon"><i class="' + icon + '"></i></div>' +
                    '<p class="upload-text">Cliquez ou glissez <strong>' + label.toLowerCase() + '</strong></p>' +
                    '<p class="upload-hint">' + hint + '</p>' +
                    '<input type="file" name="' + slug + '" id="' + slug + '" accept="' + accept + '"' + (obligatoire ? ' required' : '') + ' data-max-size="' + maxSize + '" data-types="' + types + '">' +
                    '<div class="upload-preview" id="' + slug + 'Preview">' +
                        '<img id="' + slug + 'Img" src="" alt="' + label + '">' +
                        '<button type="button" class="remove-file" onclick="removeFile(\'' + slug + '\')"><i class="fe fe-x"></i></button>' +
                    '</div>' +
                    '<div class="upload-filename" id="' + slug + 'Filename"></div>' +
                    '<div class="upload-error" id="' + slug + 'Error"></div>' +
                '</div>';

            if (index % 2 === 0) {
                currentRow = document.createElement('div');
                currentRow.className = 'form-row';
                container.appendChild(currentRow);
            }
            currentRow.appendChild(col);
            handleFileUpload(slug, slug + 'Preview', slug + 'Img', slug + 'Filename', slug + 'Error', slug + 'Zone');
        });
    }

    initDocUploads();

    // ===================== DATE EXPIRATION VALIDATION =====================
    var expDateInput = document.getElementById('date_expiration');
    var expErrorEl = document.getElementById('dateExpirationError');
    if (expDateInput) {
        function validateExpirationDate() {
            var val = expDateInput.value;
            var group = expDateInput.closest('.input-group-custom');
            if (!val) {
                if (group) group.classList.add('is-invalid');
                if (expErrorEl) { expErrorEl.style.display = 'flex'; expErrorEl.classList.add('visible'); }
                return false;
            }
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var exp = new Date(val + 'T00:00:00');
            if (exp <= today) {
                if (group) group.classList.add('is-invalid');
                if (expErrorEl) { expErrorEl.style.display = 'flex'; expErrorEl.classList.add('visible'); }
                return false;
            }
            if (group) group.classList.remove('is-invalid');
            if (expErrorEl) { expErrorEl.style.display = 'none'; expErrorEl.classList.remove('visible'); }
            return true;
        }
        expDateInput.addEventListener('input', validateExpirationDate);
        expDateInput.addEventListener('blur', validateExpirationDate);
        expDateInput.addEventListener('change', validateExpirationDate);
    }

    window.removeFile = function(inputId) {
        var input = document.getElementById(inputId);
        var preview = document.getElementById(inputId + 'Preview');
        var filename = document.getElementById(inputId + 'Filename');
        var error = document.getElementById(inputId + 'Error');
        var zone = document.getElementById(inputId + 'Zone');
        input.value = '';
        preview.classList.remove('visible');
        filename.textContent = '';
        error.classList.remove('visible');
        zone.classList.remove('has-file');
        var img = document.getElementById(inputId + 'Img');
        if (img) img.src = '';
    };

    // ===================== FORM SUBMIT =====================
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (!validateStep(3)) {
            e.preventDefault();
            goToStep(3);
            return;
        }
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
        var style = document.createElement('style');
        style.id = 'toastStyles';
        style.textContent = '@keyframes toastIn { from { opacity:0; transform:translateX(-50%) translateY(20px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }';
        document.head.appendChild(style);
    }

    // ===================== REMOVE INVALID ON INPUT =====================
    document.querySelectorAll('.input-group-custom input, .input-group-custom select').forEach(function(el) {
        el.addEventListener('input', function() {
            var group = this.closest('.input-group-custom');
            if (group) group.classList.remove('is-invalid');
        });
        el.addEventListener('change', function() {
            var group = this.closest('.input-group-custom');
            if (group) group.classList.remove('is-invalid');
        });
    });

    // ===================== INTELLIGENT PHONE PREFIX SELECTOR =====================
    var phoneCountries = [];
    try {
        var scriptEl = document.getElementById('phoneCountriesData');
        if (scriptEl) phoneCountries = JSON.parse(scriptEl.textContent);
    } catch(e) { console.error('Phone countries data error:', e); }

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
    EXTRA_PREFIXES.forEach(function(c) {
        if (!existingCodes[c.code]) phoneCountries.push(c);
    });

    phoneCountries.sort(function(a, b) {
        var aUemoa = a.indicatif && a.indicatif.length <= 4 ? 0 : 1;
        var bUemoa = b.indicatif && b.indicatif.length <= 4 ? 0 : 1;
        if (aUemoa !== bUemoa) return aUemoa - bUemoa;
        return (a.nom || '').localeCompare(b.nom || '');
    });

    var selectedPrefix = null;
    var phonePrefixBtn = document.getElementById('phonePrefixBtn');
    var phonePrefixDropdown = document.getElementById('phonePrefixDropdown');
    var phoneCode = document.getElementById('phoneCode');
    var phoneInput = document.getElementById('telephone');
    var phoneError = document.getElementById('telephoneError');

    var PHONE_RULES = {
        '+229': { min: 10, max: 10, label: '01 XX XX XX XX' },
        '+226': { min: 8, max: 8, label: 'XX XX XX XX' },
        '+225': { min: 10, max: 10, label: 'XX XX XX XX XX' },
        '+223': { min: 8, max: 8, label: 'XX XX XX XX' },
        '+227': { min: 8, max: 8, label: 'XX XX XX XX' },
        '+221': { min: 9, max: 9, label: 'XXX XX XX XX' },
        '+228': { min: 8, max: 8, label: 'XX XX XX XX' },
        '+33':  { min: 9, max: 9, label: 'X XX XX XX XX' },
        '+32':  { min: 8, max: 9, label: 'XXX XX XX XX' },
        '+41':  { min: 9, max: 9, label: 'XX XXX XX XX' },
        '+1':   { min: 10, max: 10, label: 'XXX XXX XXXX' },
        '+237': { min: 9, max: 9, label: 'XXX XX XX XX' },
    };

    function getPhoneRule(indicatif) {
        return PHONE_RULES[indicatif] || null;
    }

    function buildPhoneDropdown() {
        phonePrefixDropdown.innerHTML = '';
        phoneCountries.forEach(function(c) {
            var li = document.createElement('li');
            li.dataset.code = c.code;
            li.dataset.indicatif = c.indicatif;
            li.dataset.paysId = c.id || '';
            li.setAttribute('role', 'option');
            li.innerHTML = '<span class="pays-nom">' + (c.nom || '') + '</span><span class="pays-code">' + (c.indicatif || '') + '</span>';
            li.addEventListener('click', function(e) {
                e.stopPropagation();
                selectPhonePrefix(c.indicatif, c.nom);
                closePhoneDropdown();
            });
            phonePrefixDropdown.appendChild(li);
        });
    }

    function selectPhonePrefix(indicatif, nom) {
        selectedPrefix = indicatif;
        phoneCode.textContent = indicatif;
        phonePrefixBtn.classList.add('has-prefix');
        phoneInput.disabled = false;
        phoneInput.value = '';
        var rule = getPhoneRule(indicatif);
        phoneInput.placeholder = rule ? rule.label : 'XX XX XX XX';
        phoneInput.focus();
        validatePhoneField();
        updateStep1Button();
    }

    function togglePhoneDropdown() {
        var isOpen = phonePrefixDropdown.classList.contains('show');
        if (isOpen) closePhoneDropdown();
        else openPhoneDropdown();
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
        if (!phonePrefixBtn.contains(e.target) && !phonePrefixDropdown.contains(e.target)) {
            closePhoneDropdown();
        }
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
        validatePhoneField();
        updateStep1Button();
    });

    phoneInput.addEventListener('blur', function() {
        validatePhoneField();
        updateStep1Button();
    });

    function getPhoneDigits() {
        if (!phoneInput) return '';
        return phoneInput.value.replace(/\s/g, '');
    }

    function getFullPhone() {
        if (!selectedPrefix) return '';
        return selectedPrefix + getPhoneDigits();
    }

    function validatePhoneField() {
        if (!phoneInput) return true;
        var digits = getPhoneDigits();
        if (!selectedPrefix) {
            showPhoneError('Veuillez sélectionner l\'indicatif du pays');
            return false;
        }
        if (phoneInput.disabled) {
            showPhoneError('');
            return true;
        }
        if (digits.length === 0) {
            showPhoneError('Numéro de téléphone requis');
            return false;
        }
        var rule = getPhoneRule(selectedPrefix);
        var minLen = rule ? rule.min : 6;
        var maxLen = rule ? rule.max : 12;
        if (digits.length < minLen) {
            showPhoneError('Numéro trop court (' + minLen + ' chiffres requis)');
            return false;
        }
        if (digits.length > maxLen) {
            showPhoneError('Numéro trop long (' + maxLen + ' chiffres max)');
            return false;
        }
        showPhoneError('');
        return true;
    }

    function showPhoneError(msg) {
        var group = phoneInput.closest('.input-group-custom');
        if (group) group.classList.toggle('is-invalid', !!msg);
        if (phoneError) {
            if (msg) {
                phoneError.textContent = msg;
                phoneError.classList.add('visible');
                phoneError.style.display = 'flex';
            } else {
                phoneError.textContent = '';
                phoneError.classList.remove('visible');
                phoneError.style.display = 'none';
            }
        }
    }

    function updateStep1Button() {
        var btn = document.querySelector('.step-content[data-step="1"] .btn-next');
        if (!btn) return;
        var email = document.getElementById('email');
        var pw = document.getElementById('password');
        var pw2 = document.getElementById('password_confirmation');
        var telOk = validatePhoneField();

        var allOk = email && email.value.trim() &&
                    pw && pw.value.length >= 8 &&
                    pw2 && pw.value === pw2.value &&
                    telOk;

        if (currentPorteurType === 'personnel') {
            var prenom = document.getElementById('prenom');
            var name = document.getElementById('name');
            allOk = allOk && prenom && prenom.value.trim() && name && name.value.trim();
        } else {
            var entNom = document.getElementById('entreprise_nom');
            var entSect = document.getElementById('entreprise_secteur');
            var act = document.getElementById('activite');
            var entName = document.getElementById('name_entreprise');
            allOk = allOk && entNom && entNom.value.trim() && entSect && entSect.value && act && act.value.trim() && entName && entName.value.trim();
        }

        btn.disabled = !allOk;
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        var raw = phoneInput.value.replace(/\s/g, '');
        if (selectedPrefix) {
            if (raw.indexOf(selectedPrefix) === 0) {
                phoneInput.value = raw;
            } else {
                phoneInput.value = selectedPrefix + raw.replace(/^\+/, '');
            }
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'pays_id' && e.target.value) {
            var paysId = e.target.value;
            var match = null;
            for (var i = 0; i < phoneCountries.length; i++) {
                if (phoneCountries[i].id && String(phoneCountries[i].id) === paysId) {
                    match = phoneCountries[i];
                    break;
                }
            }
            if (match && match.indicatif && match.indicatif !== selectedPrefix) {
                selectPhonePrefix(match.indicatif, match.nom);
            }
        }
    });

    buildPhoneDropdown();
    selectPhonePrefix('+229', 'Bénin');
    // Restore phone value from old input after validation error
    var oldPhone = '{{ old('telephone') }}';
    if (oldPhone) {
        var prefix = '';
        phoneCountries.forEach(function(c) {
            if (c.indicatif && oldPhone.indexOf(c.indicatif) === 0) {
                prefix = c.indicatif;
            }
        });
        if (prefix) {
            selectPhonePrefix(prefix, '');
            phoneInput.value = oldPhone.slice(prefix.length).replace(/(\d{2})(?=\d)/g, '$1 ');
        } else {
            phoneInput.value = oldPhone.replace(/(\d{2})(?=\d)/g, '$1 ');
        }
    }
    updateStep1Button();

    // ===================== CASCADING LOCATION SELECTS =====================
    var API_BASE = '/api/v1/locations';
    var STEP2_FIELDS = ['pays_id', 'departement_id', 'commune_id', 'arrondissement_id', 'quartier_id', 'adresse'];

    function resetCascade(fromId) {
        var cascade = ['departement_id', 'commune_id', 'arrondissement_id', 'quartier_id'];
        var idx = cascade.indexOf(fromId);
        if (idx === -1) idx = 0;
        for (var i = idx; i < cascade.length; i++) {
            var el = document.getElementById(cascade[i]);
            if (!el) continue;

            if (cascade[i] === 'quartier_id') {
                quartierResetToDisabled();
                continue;
            }

            el.innerHTML = '<option value="">' + (el.getAttribute('data-placeholder') || 'Chargement...') + '</option>';
            el.disabled = true;
            el.style.display = 'block';

            if (cascade[i] === 'arrondissement_id') {
                var textEl = document.getElementById('arrondissement_nom');
                if (textEl) { textEl.value = ''; textEl.style.display = 'none'; textEl.disabled = true; }
                var btn = document.getElementById('toggleArrondissement');
                if (btn) {
                    btn.setAttribute('data-mode', 'select');
                    btn.innerHTML = 'Je ne trouve pas mon arrondissement <i class="fe fe-edit"></i>';
                    btn.style.display = 'none';
                }
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
            .then(function(r) {
                if (!r.ok) throw new Error('Erreur reseau (' + r.status + ')');
                return r.json();
            })
            .then(function(data) {
                selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
                if (data && data.length > 0) {
                    data.forEach(function(item) {
                        var opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nom;
                        selectEl.appendChild(opt);
                    });
                }
                selectEl.disabled = false;
                if (spinner) spinner.style.display = 'none';
                validateStep2Field(selectEl.id);
                var toggleId = selectEl.id === 'arrondissement_id' ? 'toggleArrondissement' : (selectEl.id === 'quartier_id' ? 'toggleQuartier' : null);
                if (toggleId) {
                    var btn = document.getElementById(toggleId);
                    if (btn) btn.style.display = 'inline-flex';
                }
            })
            .catch(function(err) {
                console.error('Location load error:', err);
                selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
                selectEl.disabled = false;
                if (spinner) spinner.style.display = 'none';
                validateStep2Field(selectEl.id);
            });
    }

    var TOGGLE_FIELDS = {
        arrondissement: { selectId: 'arrondissement_id', textId: 'arrondissement_nom', toggleId: 'toggleArrondissement' },
        quartier: { selectId: 'quartier_id', textId: 'quartier_nom', toggleId: 'toggleQuartier' }
    };

    function toggleFieldMode(field) {
        var cfg = TOGGLE_FIELDS[field];
        if (!cfg) return;
        var select = document.getElementById(cfg.selectId);
        var text = document.getElementById(cfg.textId);
        var btn = document.getElementById(cfg.toggleId);
        if (!select || !text || !btn) return;
        var mode = btn.getAttribute('data-mode');

        if (mode === 'select') {
            select.style.display = 'none';
            select.disabled = true;
            text.style.display = 'block';
            text.disabled = false;
            text.focus();
            btn.setAttribute('data-mode', 'text');
            btn.innerHTML = '<i class="fe fe-list"></i> Choisir dans la liste';

            if (field === 'arrondissement') {
                var qCfg = TOGGLE_FIELDS.quartier;
                var qSelect = document.getElementById(qCfg.selectId);
                var qText = document.getElementById(qCfg.textId);
                var qBtn = document.getElementById(qCfg.toggleId);
                if (qSelect && qBtn) {
                    qSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>';
                    qSelect.disabled = true;
                    qSelect.style.display = 'none';
                    qText.style.display = 'block';
                    qText.disabled = false;
                    qText.value = '';
                    qBtn.setAttribute('data-mode', 'text');
                    qBtn.innerHTML = '<i class="fe fe-list"></i> Choisir dans la liste';
                    qBtn.style.display = 'inline-flex';
                    showFieldError(qCfg.selectId, '');
                }
            }
        } else {
            select.style.display = 'block';
            select.disabled = false;
            text.style.display = 'none';
            text.disabled = true;
            btn.setAttribute('data-mode', 'select');
            btn.innerHTML = 'Je ne trouve pas mon ' + field + ' <i class="fe fe-edit"></i>';

            if (field === 'arrondissement') {
                quartierResetToDisabled();
                if (select.value) {
                    var quartierSelect = document.getElementById('quartier_id');
                    if (quartierSelect) {
                        var url = API_BASE + '/arrondissements/' + select.value + '/quartiers';
                        loadLocation(url, quartierSelect, 'Sélectionnez un quartier');
                    }
                }
            }
        }
        validateStep2Field(cfg.selectId);
        updateStep2Button();
    }

    function quartierResetToDisabled() {
        var qSelect = document.getElementById('quartier_id');
        var qText = document.getElementById('quartier_nom');
        var qBtn = document.getElementById('toggleQuartier');
        if (qSelect) {
            qSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>';
            qSelect.disabled = true;
            qSelect.style.display = 'block';
            qSelect.value = '';
        }
        if (qText) {
            qText.value = '';
            qText.style.display = 'none';
            qText.disabled = true;
        }
        if (qBtn) {
            qBtn.setAttribute('data-mode', 'select');
            qBtn.innerHTML = 'Je ne trouve pas mon quartier <i class="fe fe-edit"></i>';
            qBtn.style.display = 'none';
        }
        showFieldError('quartier_id', '');
        showFieldError('quartier_nom', '');
    }

    document.getElementById('toggleArrondissement').addEventListener('click', function(e) {
        e.preventDefault();
        toggleFieldMode('arrondissement');
    });
    document.getElementById('toggleQuartier').addEventListener('click', function(e) {
        e.preventDefault();
        toggleFieldMode('quartier');
    });

    function getFieldValue(id) {
        var el = document.getElementById(id);
        return el ? el.value.trim() : '';
    }

    function isFieldValid(id) {
        var val = getFieldValue(id);
        return val !== '' && val !== null;
    }

    function showFieldError(id, message) {
        var group = document.getElementById(id)?.closest('.input-group-custom');
        var errorEl = document.getElementById(id + 'Error');
        if (group) {
            if (message) group.classList.add('is-invalid');
            else group.classList.remove('is-invalid');
        }
        if (errorEl) {
            if (message) {
                errorEl.textContent = message;
                errorEl.classList.add('visible');
                errorEl.style.display = 'flex';
            } else {
                errorEl.textContent = '';
                errorEl.classList.remove('visible');
                errorEl.style.display = 'none';
            }
        }
    }

    function validateStep2Field(fieldId) {
        var el = document.getElementById(fieldId);

        var textId = null;
        if (fieldId === 'arrondissement_id') textId = 'arrondissement_nom';
        else if (fieldId === 'quartier_id') textId = 'quartier_nom';
        if (textId) {
            var textEl = document.getElementById(textId);
            if (textEl && !textEl.disabled) {
                var textVal = textEl.value.trim();
                if (!textVal || textVal.length < 2) {
                    showFieldError(fieldId, 'Veuillez saisir ce champ (min. 2 caractères)');
                    return false;
                }
                showFieldError(fieldId, '');
                return true;
            }
        }

        if (!el || el.disabled) {
            showFieldError(fieldId, '');
            return true;
        }

        var val = getFieldValue(fieldId);

        if (fieldId === 'adresse') {
            if (val.length < 3) {
                showFieldError(fieldId, 'Adresse requise (min. 3 caractères)');
                return false;
            }
            showFieldError(fieldId, '');
            return true;
        }

        if (!val) {
            var labels = {
                pays_id: 'Veuillez sélectionner un pays',
                departement_id: 'Veuillez sélectionner un département',
                commune_id: 'Veuillez sélectionner une commune',
                arrondissement_id: 'Veuillez sélectionner un arrondissement',
                quartier_id: 'Veuillez sélectionner un quartier'
            };
            showFieldError(fieldId, labels[fieldId] || 'Ce champ est requis');
            return false;
        }

        showFieldError(fieldId, '');
        return true;
    }

    function isStep2Valid() {
        for (var i = 0; i < STEP2_FIELDS.length; i++) {
            var id = STEP2_FIELDS[i];
            var el = document.getElementById(id);
            if (!el) continue;

            if (id === 'arrondissement_id' || id === 'quartier_id') {
                var textId = id === 'arrondissement_id' ? 'arrondissement_nom' : 'quartier_nom';
                var textEl = document.getElementById(textId);
                if (textEl && !textEl.disabled) {
                    var textVal = textEl.value.trim();
                    if (textVal.length < 2) return false;
                    continue;
                }
            }

            if (el.disabled) return false;
            if (!isFieldValid(id)) return false;
            if (id === 'adresse' && getFieldValue(id).length < 3) return false;
        }
        return true;
    }

    function updateStep2Button() {
        var btn = document.getElementById('step2Next');
        if (!btn) return;
        btn.disabled = !isStep2Valid();
    }

    function revalidateStep2() {
        for (var i = 0; i < STEP2_FIELDS.length; i++) {
            validateStep2Field(STEP2_FIELDS[i]);
        }
        updateStep2Button();
    }

    function setupStep2Listeners() {
        for (var i = 0; i < STEP2_FIELDS.length; i++) {
            var el = document.getElementById(STEP2_FIELDS[i]);
            if (!el) continue;
            el.addEventListener('change', function() {
                validateStep2Field(this.id);
                updateStep2Button();
            });
            el.addEventListener('input', function() {
                validateStep2Field(this.id);
                updateStep2Button();
            });
        }
        ['arrondissement_nom', 'quartier_nom'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    var selectId = this.id === 'arrondissement_nom' ? 'arrondissement_id' : 'quartier_id';
                    validateStep2Field(selectId);
                    updateStep2Button();
                });
            }
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
        if (isStep2Valid()) {
            goToStep(3);
        } else {
            showToast('Veuillez remplir tous les champs obligatoires de la localisation.', 'error');
        }
    });

    var paysSelect = document.getElementById('pays_id');
    if (paysSelect) {
        loadLocation(API_BASE + '/pays', paysSelect, 'Sélectionnez votre pays');

        paysSelect.addEventListener('change', function() {
            var payId = this.value;
            resetCascade('departement_id');
            var depSelect = document.getElementById('departement_id');
            if (payId) {
                loadLocation(API_BASE + '/pays/' + payId + '/departements', depSelect, 'Sélectionnez le département');
            } else {
                depSelect.innerHTML = '<option value="">Sélectionnez un pays d\'abord</option>';
                depSelect.disabled = true;
                revalidateStep2();
            }
        });
    }

    var depSelect = document.getElementById('departement_id');
    if (depSelect) {
        depSelect.addEventListener('change', function() {
            var depId = this.value;
            resetCascade('commune_id');
            var comSelect = document.getElementById('commune_id');
            if (depId) {
                loadLocation(API_BASE + '/departements/' + depId + '/communes', comSelect, 'Sélectionnez la commune');
            } else {
                comSelect.innerHTML = '<option value="">Sélectionnez un département d\'abord</option>';
                comSelect.disabled = true;
                revalidateStep2();
            }
        });
    }

    var comSelect = document.getElementById('commune_id');
    if (comSelect) {
        comSelect.addEventListener('change', function() {
            var comId = this.value;
            resetCascade('arrondissement_id');
            var arrSelect = document.getElementById('arrondissement_id');
            if (comId) {
                loadLocation(API_BASE + '/communes/' + comId + '/arrondissements', arrSelect, 'Sélectionnez l\'arrondissement');
            } else {
                arrSelect.innerHTML = '<option value="">Sélectionnez une commune d\'abord</option>';
                arrSelect.disabled = true;
                revalidateStep2();
            }
        });
    }

    var arrSelect = document.getElementById('arrondissement_id');
    if (arrSelect) {
        arrSelect.addEventListener('change', function() {
            var arrId = this.value;
            resetCascade('quartier_id');
            var quarSelect = document.getElementById('quartier_id');
            if (arrId) {
                loadLocation(API_BASE + '/arrondissements/' + arrId + '/quartiers', quarSelect, 'Sélectionnez le quartier');
            } else {
                quarSelect.innerHTML = '<option value="">Sélectionnez un arrondissement d\'abord</option>';
                quarSelect.disabled = true;
                revalidateStep2();
            }
        });
    }
})();
</script>
@endpush

@extends('layouts.auth')

@push('title')
    Réinitialisation mot de passe
@endpush

@section('content')
    <div class="login-card" style="justify-content: center;">
        <a href="{{ route('login') }}" class="back-home-link">
            <i class="fe fe-arrow-left"></i> Retour à la connexion
        </a>

        <div class="login-logo">
            <img src="{{ url('frontend/asset/images/brand/logo-dark.png') }}" alt="Alogoto">
        </div>

        <div class="login-title">
            <h2>Nouveau mot de passe</h2>
            <p>Choisissez un mot de passe sécurisé</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <i class="fe fe-alert-circle"></i>
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Adresse email</label>
                <div class="input-group-custom @error('email') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-mail"></i></span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="ex: vous@email.com"
                        required
                        autocomplete="email"
                    >
                </div>
                @error('email')
                    <div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <div class="input-group-custom @error('password') is-invalid @enderror">
                    <span class="input-icon"><i class="fe fe-lock"></i></span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Minimum 8 caractères"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                    <button type="button" class="toggle-password" aria-label="Afficher le mot de passe">
                        <i class="fe fe-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="fe fe-lock"></i></span>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Retaper le mot de passe"
                        required
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <button type="submit" class="btn-login">
                <span class="btn-text">Réinitialiser</span>
                <span class="spinner"></span>
            </button>
        </form>

        <div class="login-footer">
            <p>
                <a href="{{ route('login') }}">Retour à la connexion</a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.closest('.input-group-custom').querySelector('input');
            var isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            this.querySelector('i').className = isPassword ? 'fe fe-eye-off' : 'fe fe-eye';
        });
    });
</script>
@endpush

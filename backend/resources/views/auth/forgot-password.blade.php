@extends('layouts.auth')

@push('title')
    Mot de passe oublié
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
            <h2>Mot de passe oublié</h2>
            <p>Saisissez votre email pour recevoir un lien de réinitialisation</p>
        </div>

        @if (session('status'))
            <div class="alert-success">
                <i class="fe fe-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <i class="fe fe-alert-circle"></i>
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

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
                        autofocus
                        autocomplete="email"
                    >
                </div>
                @error('email')
                    <div class="field-error"><i class="fe fe-alert-triangle"></i> {{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">
                <span class="btn-text">Envoyer le lien</span>
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
    document.querySelector('.btn-login')?.addEventListener('click', function() {
        this.classList.add('loading');
    });
</script>
@endpush

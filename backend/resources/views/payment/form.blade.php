@extends('layouts.app')

@section('title', 'Paiement - ' . $project->titre)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-credit-card"></i>
                        Paiement FedaPay
                    </h4>
                </div>
                
                <div class="card-body">
                    <!-- Informations projet -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Projet à financer</h6>
                        <hr>
                        <p class="mb-1"><strong>Titre:</strong> {{ $project->titre }}</p>
                        <p class="mb-1"><strong>Secteur:</strong> {{ $project->secteur }}</p>
                        <p class="mb-0"><strong>Montant:</strong> 
                            <span class="fs-4 text-primary">{{ number_format($project->montant_demande, 0, ',', ' ') }} FCFA</span>
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Formulaire de paiement -->
                    <form action="{{ route('payment.initiate', $project) }}" method="POST">
                        @csrf

                        <h6 class="mb-3">Informations du payeur</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Prénom *</label>
                                <input type="text" 
                                       class="form-control @error('firstname') is-invalid @enderror" 
                                       id="firstname" 
                                       name="firstname" 
                                       value="{{ old('firstname', auth()->user()->name ?? '') }}"
                                       required>
                                @error('firstname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Nom *</label>
                                <input type="text" 
                                       class="form-control @error('lastname') is-invalid @enderror" 
                                       id="lastname" 
                                       name="lastname" 
                                       value="{{ old('lastname') }}"
                                       required>
                                @error('lastname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', auth()->user()->email ?? '') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone *</label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="+229 XX XX XX XX"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Méthode de paiement *</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="mobile_money" value="mobile_money" checked>
                                    <label class="btn btn-outline-primary w-100" for="mobile_money">
                                        <i class="bi bi-phone"></i><br>
                                        Mobile Money
                                    </label>
                                </div>
                                
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="card" value="card">
                                    <label class="btn btn-outline-primary w-100" for="card">
                                        <i class="bi bi-credit-card"></i><br>
                                        Carte bancaire
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-lock-fill"></i>
                                Payer {{ number_format($project->montant_demande, 0, ',', ' ') }} FCFA
                            </button>
                            
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Annuler
                            </a>
                        </div>
                    </form>

                    <!-- Informations sécurité -->
                    <div class="mt-4 text-center text-muted">
                        <small>
                            <i class="bi bi-shield-check"></i>
                            Paiement sécurisé par FedaPay
                        </small>
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle"></i>
                        Besoin d'aide?
                    </h6>
                    <p class="card-text small text-muted mb-0">
                        En cas de problème avec le paiement, contactez notre support:
                        <br>
                        <a href="mailto:support@alogoto.com">support@alogoto.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

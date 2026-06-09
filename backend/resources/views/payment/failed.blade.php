@extends('layouts.app')

@section('title', 'Paiement échoué')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle text-danger" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="text-danger mb-3">Paiement échoué</h2>
                    
                    <p class="lead mb-4">
                        {{ $message ?? 'Votre paiement n\'a pas pu être traité.' }}
                    </p>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Que faire?</h6>
                        <ul class="text-start mb-0">
                            <li>Vérifiez que votre compte est suffisamment approvisionné</li>
                            <li>Essayez avec une autre méthode de paiement</li>
                            <li>Vérifiez vos informations de paiement</li>
                            <li>Contactez votre opérateur si le problème persiste</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-repeat"></i>
                            Réessayer
                        </a>
                        
                        <a href="{{ route('dashboard.porteur') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-speedometer2"></i>
                            Retour au dashboard
                        </a>
                    </div>

                    <div class="mt-4 text-muted">
                        <small>
                            Besoin d'aide? Contactez-nous: 
                            <a href="mailto:support@alogoto.com">support@alogoto.com</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

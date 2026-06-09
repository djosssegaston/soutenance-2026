@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Paiement réussi!</h2>
                    
                    <p class="lead mb-4">
                        Votre paiement a été traité avec succès.
                    </p>

                    @if(isset($transaction_id))
                        <div class="alert alert-info">
                            <strong>Transaction ID:</strong> {{ $transaction_id }}
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Vous recevrez une confirmation par email shortly.
                    </p>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('dashboard.porteur') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-speedometer2"></i>
                            Voir mon dashboard
                        </a>
                        
                        <a href="{{ route('payment.status', $transaction_id ?? '') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-receipt"></i>
                            Voir le reçu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

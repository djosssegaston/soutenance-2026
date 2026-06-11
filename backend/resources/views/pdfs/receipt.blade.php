<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de paiement {{ $receipt_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; }
        .receipt-wrapper { max-width: 100%; margin: 0 auto; }
        .receipt-header { text-align: center; border-bottom: 2px dashed #dee2e6; padding-bottom: 14px; margin-bottom: 18px; }
        .receipt-header h2 { font-weight: 700; color: #e67e22; margin: 0 0 4px 0; font-size: 18px; }
        .receipt-header .receipt-number { font-size: 12px; color: #6b7a8f; }
        .receipt-header .date { font-size: 11px; color: #6b7a8f; margin-top: 2px; }
        .section-title { font-weight: 600; font-size: 12px; color: #2d3748; margin: 14px 0 8px 0; padding-bottom: 4px; border-bottom: 1px solid #e9ecef; }
        .row-item { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dotted #e9ecef; }
        .row-item .label { color: #6b7a8f; }
        .row-item .value { font-weight: 600; color: #2d3748; text-align: right; }
        .receipt-total { background: #fff8f0; border: 2px solid #fde4c8; padding: 12px; margin-top: 14px; }
        .receipt-total .row-item { border-bottom: none; padding: 4px 0; }
        .receipt-total .row-item.total { border-top: 2px solid #e67e22; margin-top: 4px; padding-top: 8px; }
        .receipt-total .row-item.total .value { font-size: 16px; color: #d35400; }
        .receipt-footer { text-align: center; margin-top: 18px; padding-top: 14px; border-top: 2px dashed #dee2e6; font-size: 10px; color: #9aa9bb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 600; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div class="receipt-header">
            <h2>ALOGOTO</h2>
            <div class="receipt-number">Reçu de paiement N° <strong>{{ $receipt_number }}</strong></div>
            <div class="date">Date d'émission : {{ $date }}</div>
        </div>

        <div class="section-title">Projet</div>
        <div class="row-item">
            <span class="label">Titre</span>
            <span class="value">{{ $project?->titre ?? '' }}</span>
        </div>
        <div class="row-item">
            <span class="label">Code projet</span>
            <span class="value">PRJ-{{ str_pad((string) ($project?->id ?? 0), 3, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="section-title">Institution</div>
        <div class="row-item">
            <span class="label">Nom</span>
            <span class="value">{{ $institution?->nom ?? '' }}</span>
        </div>

        <div class="section-title">Porteur</div>
        <div class="row-item">
            <span class="label">Nom</span>
            <span class="value">{{ $porteur?->name ?? '' }}</span>
        </div>
        <div class="row-item">
            <span class="label">Email</span>
            <span class="value">{{ $porteur?->email ?? '' }}</span>
        </div>

        <div class="section-title">Paiement</div>
        <div class="row-item">
            <span class="label">Montant total</span>
            <span class="value">{{ number_format((float) $repayment->montant_total, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="row-item">
            <span class="label">Montant remboursé</span>
            <span class="value">{{ number_format((float) $repayment->montant_rembourse, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="row-item">
            <span class="label">Date d'échéance</span>
            <span class="value">{{ $repayment->date_echeance?->format('d/m/Y') ?? '' }}</span>
        </div>
        <div class="row-item">
            <span class="label">Date de paiement</span>
            <span class="value">{{ $repayment->date_paiement?->format('d/m/Y') ?? '' }}</span>
        </div>
        <div class="row-item">
            <span class="label">Statut</span>
            <span class="value"><span class="badge badge-success">{{ $repayment->statut === 'paye' ? 'Payé' : ($repayment->statut ?? 'Inconnu') }}</span></span>
        </div>

        <div class="receipt-total">
            <div class="row-item">
                <span class="label">Montant versé</span>
                <span class="value" style="font-size:14px;">{{ number_format((float) $repayment->montant_rembourse, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="receipt-footer">
            Généré par {{ $generated_by }} &bull; {{ $date }}<br>
            <strong>Alogoto</strong> &mdash; Solution de financement participatif
        </div>
    </div>
</body>
</html>

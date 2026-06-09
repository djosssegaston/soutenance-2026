<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Convocation - Entretien #{{ $interview->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0048DC; padding-bottom: 15px; }
        .header h1 { color: #0048DC; font-size: 20px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 11px; margin: 2px 0; }
        .ref { text-align: right; font-size: 10px; color: #999; margin-bottom: 20px; }
        .content { margin: 20px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 8px 12px; border: 1px solid #ddd; }
        .info-table td:first-child { font-weight: bold; width: 35%; background: #f8f9fa; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #999; text-align: center; }
        .signature { margin-top: 50px; }
        .signature div { display: inline-block; width: 45%; text-align: center; }
        .signature .line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONVOCATION À UN ENTRETIEN</h1>
        <p>{{ $institution->nom }}</p>
        <p>Plateforme ALOGOTO - Financement Participatif</p>
    </div>

    <div class="ref">
        Réf: CONV-{{ date('Y') }}-{{ str_pad((string) $interview->id, 4, '0', STR_PAD_LEFT) }}
        <br>Date: {{ $date }}
    </div>

    <div class="content">
        <p><strong>Objet :</strong> Convocation à un entretien d'évaluation de projet</p>
        <p>Madame, Monsieur <strong>{{ $porteur->name }}</strong>,</p>
        <p>Nous avons le plaisir de vous convier à un entretien dans le cadre de l'évaluation de votre projet <strong>"{{ $project->titre }}"</strong>.</p>

        <h3>Détails de l'entretien</h3>
        <table class="info-table">
            <tr><td>Titre</td><td>{{ $interview->titre }}</td></tr>
            <tr><td>Date</td><td>{{ \Carbon\Carbon::parse($interview->date_entretien)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</td></tr>
            <tr><td>Heure</td><td>{{ substr($interview->heure_entretien, 0, 5) }}</td></tr>
            <tr><td>Type</td><td>{{ ucfirst($interview->type_entretien) }}</td></tr>
            @if($interview->lieu)
            <tr><td>Lieu</td><td>{{ $interview->lieu }}</td></tr>
            @endif
            @if($interview->description)
            <tr><td>Description</td><td>{{ $interview->description }}</td></tr>
            @endif
        </table>

        @if($interview->type_entretien === 'visio')
        <p><strong>Note :</strong> Un lien de visioconférence vous sera communiqué ultérieurement.</p>
        @endif

        <p>Nous vous prions de bien vouloir confirmer votre présence par l'intermédiaire de votre espace personnel sur la plateforme ALOGOTO.</p>
    </div>

    <div class="signature">
        <div>
            <div class="line">Signature du porteur</div>
        </div>
        <div>
            <div class="line">Signature de l'institution</div>
        </div>
    </div>

    <div class="footer">
        <p>ALOGOTO - Plateforme de Financement Participatif | {{ $institution->email ?? 'contact@alogoto.com' }} | {{ $institution->telephone ?? '' }}</p>
        <p>Document généré le {{ now()->locale('fr')->isoFormat('D MMMM YYYY à HH:mm') }}</p>
    </div>
</body>
</html>

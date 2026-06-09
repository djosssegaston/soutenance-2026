<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="no-referrer">
    <title>Documents KYC sécurisés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body { height: 100%; margin: 0; overflow: auto; background: #1a1a2e; color: #fff; }
        .kyc-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .kyc-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .kyc-header h4 { margin: 0; }
        .kyc-card { background: #16213e; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
        .kyc-card h5 { color: #e94560; margin-bottom: 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .kyc-card img { max-width: 100%; border-radius: 8px; cursor: pointer; transition: transform 0.2s; }
        .kyc-card img:active { transform: scale(1.8); }
        .secure-footer { text-align: center; padding: 12px; color: #666; font-size: 12px; border-top: 1px solid #0f3460; margin-top: 24px; }
        .lock-badge { color: #f0c040; font-size: 13px; }
    </style>
</head>
<body>
    <div class="kyc-container">
        <div class="kyc-header">
            <h4><i class="bi bi-file-earmark-lock2 text-info me-2"></i>Documents KYC</h4>
            <div>
                @if (! $isOwner)
                    <span class="lock-badge me-3"><i class="bi bi-lock-fill"></i> Consultation seule</span>
                @endif
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close()">
                    <i class="bi bi-x-lg"></i> Fermer
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="kyc-card">
                    <h5><i class="bi bi-card-image me-1"></i> Recto</h5>
                    @if ($document->recto_path)
                        <img src="{{ route('secure.documents.kyc.serve', ['document' => $document->id, 'field' => 'recto']) }}" alt="Recto" class="img-fluid" />
                    @else
                        <p class="text-muted">Non fourni</p>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="kyc-card">
                    <h5><i class="bi bi-card-image me-1"></i> Verso</h5>
                    @if ($document->verso_path)
                        <img src="{{ route('secure.documents.kyc.serve', ['document' => $document->id, 'field' => 'verso']) }}" alt="Verso" class="img-fluid" />
                    @else
                        <p class="text-muted">Non fourni</p>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="kyc-card">
                    <h5><i class="bi bi-camera me-1"></i> Selfie</h5>
                    @if ($document->selfie_path)
                        <img src="{{ route('secure.documents.kyc.serve', ['document' => $document->id, 'field' => 'selfie']) }}" alt="Selfie" class="img-fluid" />
                    @else
                        <p class="text-muted">Non fourni</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="secure-footer">Document confidentiel - Accès restreint</div>
    </div>

    <script>
        (function() {
            document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
            document.addEventListener('keydown', function(e) {
                if (
                    e.ctrlKey && ['s', 'p', 'u', 'S', 'P', 'U', 'c', 'C'].includes(e.key) ||
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && ['i', 'I', 'j', 'J', 'c', 'C'].includes(e.key))
                ) { e.preventDefault(); }
            });
            document.addEventListener('dragstart', function(e) { e.preventDefault(); });
            document.addEventListener('selectstart', function(e) { if (! e.target.closest('.kyc-header')) e.preventDefault(); });
            var style = document.createElement('style');
            style.media = 'print';
            style.textContent = 'body { display: none !important; }';
            document.head.appendChild(style);
        })();
    </script>
</body>
</html>

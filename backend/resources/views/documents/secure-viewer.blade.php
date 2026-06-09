<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="no-referrer">
    <title>Visualisation sécurisée - {{ $fileName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        html, body { height: 100%; margin: 0; overflow: hidden; background: #1a1a2e; }
        .secure-viewer { display: flex; flex-direction: column; height: 100vh; }
        .secure-toolbar { background: #16213e; color: #fff; padding: 8px 16px; display: flex; align-items: center; gap: 12px; flex-shrink: 0; border-bottom: 1px solid #0f3460; }
        .secure-toolbar .file-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 14px; }
        .secure-toolbar .badge-ext { background: #0f3460; color: #e94560; font-size: 11px; padding: 2px 8px; border-radius: 4px; }
        .secure-frame { flex: 1; border: none; width: 100%; background: #fff; }
        .lock-badge { color: #f0c040; font-size: 13px; }
        .secure-toolbar .btn { font-size: 13px; }
        .not-viewable { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #6c757d; }
        .not-viewable i { font-size: 64px; margin-bottom: 16px; }
        .secure-footer { background: #16213e; color: #888; font-size: 11px; text-align: center; padding: 4px; flex-shrink: 0; border-top: 1px solid #0f3460; }
    </style>
</head>
<body>
    <div class="secure-viewer">
        <div class="secure-toolbar">
            <i class="bi bi-file-earmark-lock2 text-info"></i>
            <span class="file-name" title="{{ $fileName }}">{{ $fileName }}</span>
            <span class="badge badge-ext">{{ strtoupper($ext) }}</span>

            @if (! $isOwner)
                <span class="lock-badge"><i class="bi bi-lock-fill"></i> Consultation seule</span>
            @endif

            <a href="{{ $serveUrl }}" class="btn btn-outline-light btn-sm" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-box-arrow-up-right"></i> Ouvrir
            </a>

            @if ($canDownload)
                <a href="{{ $downloadUrl }}" class="btn btn-success btn-sm">
                    <i class="bi bi-download"></i> Télécharger
                </a>
            @endif

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.history.back()">
                <i class="bi bi-x-lg"></i> Fermer
            </button>
        </div>

        @if ($ext === 'pdf')
            <div class="secure-frame" style="display:flex;flex-direction:column;align-items:center;justify-content:center;background:#fff;">
                <i class="bi bi-file-earmark-pdf" style="font-size:64px;color:#e74c3c;margin-bottom:16px;"></i>
                <h5 style="color:#555;margin-bottom:8px;">Document PDF</h5>
                <p style="color:#999;margin-bottom:16px;">{{ $fileName }}</p>
                <a href="{{ $serveUrl }}" class="btn btn-danger" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-box-arrow-up-right"></i> Ouvrir le PDF
                </a>
            </div>
        @elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            <div class="secure-frame" style="background:#fff;display:flex;align-items:center;justify-content:center;">
                <img id="documentImage" style="max-width:100%;max-height:100%;object-fit:contain;" alt="{{ $fileName }}">
            </div>
        @else
            <div class="not-viewable">
                <i class="bi bi-file-earmark"></i>
                <h5>Ce type de fichier ne peut pas être affiché</h5>
                <p class="text-muted">Format non supporté par le visualiseur intégré.</p>
                @if ($canDownload)
                    <a href="{{ $downloadUrl }}" class="btn btn-primary">
                        <i class="bi bi-download"></i> Télécharger
                    </a>
                @endif
            </div>
        @endif

        <div class="secure-footer">Document confidentiel - Accès restreint</div>
    </div>

    <script>
        (function() {
            var img = document.getElementById('documentImage');
            if (img) {
                fetch('{{ $serveUrl }}', { credentials: 'same-origin', redirect: 'error' })
                    .then(function(r) {
                        if (! r.ok) throw new Error('HTTP ' + r.status);
                        return r.blob();
                    })
                    .then(function(blob) {
                        img.src = URL.createObjectURL(blob);
                    })
                    .catch(function() {
                        img.alt = 'Document non accessible';
                    });
            }

            document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
            document.addEventListener('keydown', function(e) {
                if (
                    e.ctrlKey && ['s', 'p', 'u', 'S', 'P', 'U', 'c', 'C'].includes(e.key) ||
                    e.key === 'F12' ||
                    (e.ctrlKey && e.shiftKey && ['i', 'I', 'j', 'J', 'c', 'C'].includes(e.key))
                ) {
                    e.preventDefault();
                }
            });
            document.addEventListener('dragstart', function(e) { e.preventDefault(); });
            document.addEventListener('selectstart', function(e) {
                if (! e.target.closest('.secure-toolbar')) e.preventDefault();
            });
            window.addEventListener('beforeprint', function(e) {
                e.preventDefault();
                window.location.href = 'about:blank';
            });
            var style = document.createElement('style');
            style.media = 'print';
            style.textContent = 'body { display: none !important; }';
            document.head.appendChild(style);
        })();
    </script>
</body>
</html>

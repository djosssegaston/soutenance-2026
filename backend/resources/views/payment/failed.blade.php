<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement échoué - Alogoto</title>
    <link href="{{ url('frontend/asset/css/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/style.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/icons.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0d4a7a 0%, #1a6bb0 50%, #0d4a7a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .checkout-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        .checkout-header {
            background: linear-gradient(135deg, #0d4a7a, #1a6bb0);
            padding: 30px 30px 24px;
            text-align: center;
            animation: fadeIn 0.3s ease-out;
        }

        .checkout-header img {
            height: 36px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .checkout-body {
            padding: 28px 30px;
            text-align: center;
            animation: fadeIn 0.3s ease-out 0.1s both;
        }

        .checkout-body .icon {
            margin-bottom: 16px;
        }

        .checkout-body h2 {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .checkout-body .message {
            font-size: 14px;
            color: #6b7a8f;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .checkout-body .action-btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
            margin: 4px;
        }

        .checkout-body .action-btn:hover {
            transform: translateY(-1px);
        }

        .checkout-body .btn-retry {
            background: linear-gradient(135deg, #1a6bb0, #0d4a7a);
            color: #fff;
            border: none;
        }

        .checkout-body .btn-retry:hover {
            box-shadow: 0 4px 15px rgba(13, 74, 122, 0.3);
            color: #fff;
        }

        .checkout-body .btn-dashboard {
            background: #f0f2f5;
            color: #3d4a5c;
            border: none;
        }

        .checkout-body .btn-dashboard:hover {
            background: #e4e8ee;
            color: #3d4a5c;
        }

        .checkout-footer {
            text-align: center;
            padding: 0 30px 24px;
            font-size: 12px;
            color: #9aa9bb;
        }

        .checkout-footer .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #6b7a8f;
            background: #f0f2f5;
            padding: 4px 12px;
            border-radius: 20px;
        }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .checkout-header { padding: 24px 20px 18px; }
            .checkout-body { padding: 22px 20px; }
            .checkout-footer { padding: 0 20px 20px; }
        }
    </style>
</head>
<body>
    <div class="checkout-card">
        <div class="checkout-header">
            <img src="{{ url('frontend/asset/images/brand/logo-white.png') }}" alt="Alogoto" onerror="this.style.display='none'">
        </div>

        <div class="checkout-body">
            <div class="icon">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M15 9l-6 6M9 9l6 6" stroke-linecap="round"/>
                </svg>
            </div>

            <h2>Paiement échoué</h2>

            <p class="message">{{ $message ?? 'Votre paiement n\'a pas pu être traité.' }}</p>

            <div>
                <a href="{{ $return_url ?? url('frontend/dashboard02/porteur') }}" class="action-btn btn-retry">
                    <i class="bi bi-arrow-repeat"></i> Réessayer
                </a>
                <a href="{{ url('frontend/dashboard02/porteur') }}" class="action-btn btn-dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="checkout-footer">
            <div class="badge">
                <i class="bi bi-shield-check"></i>
                Connexion sécurisée
            </div>
        </div>
    </div>
</body>
</html>
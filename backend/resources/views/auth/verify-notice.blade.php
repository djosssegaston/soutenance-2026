<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification d'email - Alogoto</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(180deg, #edf4f2 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 12px 32px rgba(6, 42, 38, 0.10);
            text-align: center;
        }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h1 { font-size: 24px; color: #1a202c; margin-bottom: 12px; }
        p { color: #4a5568; line-height: 1.6; margin-bottom: 8px; }
        .btn {
            display: inline-block;
            margin-top: 24px;
            padding: 12px 32px;
            background: #0f766e;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .btn:hover { background: #115e59; }
        .resend { margin-top: 16px; font-size: 14px; }
        .resend a { color: #0f766e; }
        .success { color: #16a34a; font-weight: 600; margin-top: 12px; }
        .error { color: #dc2626; font-weight: 600; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">📧</div>
        <h1>Vérifiez votre adresse email</h1>
        <p>Un lien de vérification a été envoyé à votre adresse email.</p>
        <p>Veuillez cliquer sur le lien dans l'email pour activer votre compte et accéder au tableau de bord.</p>

        @if (session('status') === 'verification-link-sent')
            <p class="success">Un nouveau lien de vérification a été envoyé.</p>
        @endif

        @if (session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" style="margin-top: 20px;">
            @csrf
            <button type="submit" class="btn" style="background: #f09a16;">Renvoyer l'email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
            @csrf
            <button type="submit" class="btn" style="background: #64748b; font-size: 13px;">Retour à la connexion</button>
        </form>
    </div>
</body>
</html>

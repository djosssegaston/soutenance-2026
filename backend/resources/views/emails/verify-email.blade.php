<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmez votre compte</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f5; font-family: 'Segoe UI', Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f5; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 18px 40px rgba(6,42,38,0.12);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f3d35,#1b6b5a); padding:24px 32px; text-align:left;">
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="height:44px; display:block; margin-bottom:10px;">
                            <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:700;">Confirmez votre compte</h1>
                            <p style="margin:8px 0 0; color:#e5f2ef; font-size:14px;">Finalisez votre inscription en une seule etape.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 12px; color:#0f2f2a;">
                            <p style="margin:0 0 12px; font-size:15px;">Bonjour {{ $userName }},</p>
                            <p style="margin:0 0 14px; font-size:14.5px; line-height:1.6; color:#3d4a47;">
                                Merci d'avoir cree votre compte sur <strong>{{ $appName }}</strong>. Pour activer votre espace et acceder a la plateforme, veuillez confirmer votre adresse email.
                            </p>
                            <div style="text-align:center; margin:24px 0 20px;">
                                <a href="{{ $verificationUrl }}" style="background:#f09a16; color:#ffffff; text-decoration:none; font-weight:700; padding:12px 26px; border-radius:999px; display:inline-block;">
                                    Je confirme mon compte
                                </a>
                            </div>
                            <p style="margin:0 0 8px; font-size:13.5px; color:#5b6663;">
                                Ce lien est valable 60 minutes.
                            </p>
                            <p style="margin:0 0 8px; font-size:13.5px; color:#5b6663;">
                                Si vous n'etes pas a l'origine de cette inscription, ignorez simplement cet email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e8eeec; padding-top:14px;">
                                <tr>
                                    <td style="font-size:12.5px; color:#7c8784;">
                                        Besoin d'aide ? Contactez-nous :
                                        <a href="mailto:{{ $supportEmail }}" style="color:#1b6b5a; text-decoration:none;">{{ $supportEmail }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0; font-size:12px; color:#98a09d;">{{ $appName }} · Tous droits reserves</p>
            </td>
        </tr>
    </table>
</body>
</html>

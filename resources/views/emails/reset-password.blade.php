<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - Medix eSanté</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F8FAFC;
            color: #1e293b;
            padding: 40px 16px;
        }
        .wrapper {
            max-width: 520px;
            margin: 0 auto;
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0066FF 0%, #0D9488 100%);
            padding: 36px 40px;
            text-align: center;
        }
        .logo-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4);
            margin-bottom: 12px;
        }
        .logo-circle svg { width: 26px; height: 26px; }
        .header h1 {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .body {
            padding: 40px 40px 32px;
        }
        .body h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .body p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #0066FF 0%, #0044cc 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 14px 36px;
            border-radius: 12px;
            margin-bottom: 28px;
        }
        .url-box {
            background: #F1F5F9;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #64748b;
            word-break: break-all;
            margin-bottom: 24px;
        }
        .expire-note {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 0 !important;
        }
        .footer {
            background: #F8FAFC;
            border-top: 1px solid #e2e8f0;
            padding: 20px 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="logo-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20V8l8 7 8-7v12"/>
                        <path d="M12 3v4M10 5h4" stroke-width="2"/>
                    </svg>
                </div>
                <h1>Medix eSanté</h1>
            </div>

            <!-- Body -->
            <div class="body">
                <h2>Réinitialisation du mot de passe</h2>
                <p>
                    Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte.<br>
                    Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
                </p>

                <a href="{{ $url }}" class="btn">Réinitialiser mon mot de passe</a>

                <p>Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur&nbsp;:</p>
                <div class="url-box">{{ $url }}</div>

                <p class="expire-note">
                    Ce lien expirera dans {{ config('auth.passwords.users.expire', 60) }} minutes.<br>
                    Si vous n'avez pas demandé cette réinitialisation, ignorez cet email — votre mot de passe reste inchangé.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                &copy; {{ date('Y') }} Medix eSanté — Plateforme de santé numérique sécurisée
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Medix eSanté' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F8FAFC;
            color: #1e293b;
            padding: 40px 16px;
        }
        .wrapper { max-width: 520px; margin: 0 auto; }
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
        .body { padding: 40px 40px 32px; }
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
            margin-bottom: 24px;
        }
        .icon-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            margin-bottom: 20px;
        }
        .icon-badge svg { width: 24px; height: 24px; }
        .icon-blue { background: #EFF6FF; }
        .icon-blue svg { stroke: #0066FF; }
        .icon-teal { background: #F0FDFA; }
        .icon-teal svg { stroke: #0D9488; }
        .icon-amber { background: #FFFBEB; }
        .icon-amber svg { stroke: #D97706; }
        .icon-red { background: #FEF2F2; }
        .icon-red svg { stroke: #DC2626; }
        .icon-green { background: #F0FDF4; }
        .icon-green svg { stroke: #16A34A; }
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
        .btn-teal {
            background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%);
        }
        .btn-amber {
            background: linear-gradient(135deg, #D97706 0%, #B45309 100%);
        }
        .info-box {
            background: #F1F5F9;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .info-box p {
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 0;
        }
        .info-box strong { color: #1e293b; }
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 24px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #94a3b8; }
        .detail-value { color: #1e293b; font-weight: 600; }
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
        .footer a { color: #0066FF; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <div class="logo-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12h6m-3-3v6"/>
                        <rect x="3" y="3" width="18" height="18" rx="4"/>
                    </svg>
                </div>
                <h1>Medix eSanté</h1>
            </div>

            <div class="body">
                @yield('content')
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} <a href="{{ config('app.url') }}">Medix eSanté</a> — Plateforme de santé numérique sécurisée<br>
                <span style="font-size:10px; color:#cbd5e1;">Cet email a été envoyé automatiquement, merci de ne pas y répondre.</span>
            </div>
        </div>
    </div>
</body>
</html>

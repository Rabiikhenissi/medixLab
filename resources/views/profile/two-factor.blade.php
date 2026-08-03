@php
    $groupCode = auth()->user()->group->code ?? 'admin';
    $layouts = [
        'admin'   => 'layouts.admin',
        'doctor'  => 'components.layouts.doctor',
        'patient' => 'components.layouts.patient',
        'center'  => 'layouts.center',
    ];
@endphp
@php
    $roleColors = [
        'admin'  => ['primary' => '#1e293b', 'dark' => '#0f172a', 'light' => 'rgba(30,41,59,0.1)'],
        'doctor' => ['primary' => '#0066ff', 'dark' => '#0044cc', 'light' => 'rgba(0,102,255,0.1)'],
        'patient'=> ['primary' => '#0D9488', 'dark' => '#0a7a70', 'light' => 'rgba(13,148,136,0.1)'],
        'center' => ['primary' => '#7C3AED', 'dark' => '#6d28d9', 'light' => 'rgba(124,58,237,0.1)'],
    ];
    $colors = $roleColors[$groupCode] ?? $roleColors['admin'];
    $enabled = auth()->user()->twoFactorEnabled();
@endphp

@extends($layouts[$groupCode] ?? 'layouts.admin')

@section('title', 'Sécurité - Authentification à deux facteurs')

@section('page-title')
Sécurité <span style="color:{{ $colors['primary'] }};">2FA</span>
@endsection

@section('page-subtitle')
Activez la double authentification pour protéger votre compte.
@endsection

@section('content')

<div class="max-w-2xl">
    @if(session('success'))
        <div class="mb-4 p-3 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-card">
        <h3>Authentification à deux facteurs (TOTP)</h3>
        <p style="font-size:13px; color:#64748b; line-height:1.6; margin-bottom:16px;">
            Renforcez la sécurité de votre compte : en plus du mot de passe, un code à 6 chiffres
            généré par votre application d'authentification (Google Authenticator, Authy, 1Password…) sera
            demandé à chaque connexion.
        </p>

        <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:12px;
            {{ $enabled ? 'background:#ecfdf5; border:1px solid #a7f3d0;' : 'background:#fffbeb; border:1px solid #fde68a;' }}">
            <span style="font-size:20px;">{{ $enabled ? '✅' : '⚠️' }}</span>
            <div>
                <strong style="font-size:13px; color:#0f172a;">
                    {{ $enabled ? 'Activée' : 'Non activée' }}
                </strong>
                @if($enabled)
                    <div style="font-size:12px; color:#475569;">
                        Activée le {{ auth()->user()->two_factor_confirmed_at?->format('d/m/Y à H:i') }}
                    </div>
                @else
                    <div style="font-size:12px; color:#475569;">Votre compte n'est pas encore protégé.</div>
                @endif
            </div>
        </div>
    </div>

    @if($enabled)
        <div class="form-card" style="margin-top:20px;">
            <h3>Désactiver la double authentification</h3>
            <form action="{{ route('profile.two-factor.disable') }}" method="POST">
                @csrf
                <div class="form-field full">
                    <label>Mot de passe actuel</label>
                    <input type="password" name="password" required>
                    @error('password')
                        <span style="font-size:11px; color:#e11d48;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save" style="background:#e11d48; border-color:#e11d48;">
                        Désactiver la 2FA
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="form-card" style="margin-top:20px;">
            <h3>Activer la double authentification</h3>
            <ol style="font-size:13px; color:#475569; line-height:1.8; padding-left:20px; margin-bottom:18px;">
                <li>Scannez le code QR ci-dessous avec votre application d'authentification.</li>
                <li>Si le scan est impossible, saisissez la clé manuellement.</li>
                <li>Saisissez ensuite le code à 6 chiffres affiché par l'application.</li>
            </ol>

            <div style="display:flex; justify-content:center; margin-bottom:18px;">
                {!! $qrCode !!}
            </div>

            <div style="text-align:center; margin-bottom:18px;">
                <span style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Clé secrète</span>
                <div style="margin-top:6px; font-family:monospace; font-size:14px; letter-spacing:0.08em; color:#0f172a; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; padding:10px;">
                    {{ $secret }}
                </div>
                <div style="margin-top:6px; font-size:11px; color:#94a3b8;">
                    {{ $otpauthUrl }}
                </div>
            </div>

            <form action="{{ route('profile.two-factor.enable') }}" method="POST">
                @csrf
                <div class="form-field full">
                    <label>Code à 6 chiffres</label>
                    <input type="text" name="code" maxlength="6" inputmode="numeric" placeholder="••••••" required>
                    @error('code')
                        <span style="font-size:11px; color:#e11d48;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">Activer la 2FA</button>
                </div>
            </form>
        </div>
    @endif
</div>

@endsection

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
    $email = auth()->user()->email;
@endphp

@extends($layouts[$groupCode] ?? 'layouts.admin')

@section('title', 'Sécurité - Authentification à deux facteurs')

@section('page-title')
Sécurité <span style="color:{{ $colors['primary'] }};">2FA</span>
@endsection

@section('page-subtitle')
Protégez votre compte avec une double authentification par email.
@endsection

@section('content')

<div class="max-w-2xl">
    @if(session('success'))
        <div class="mb-4 p-3 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('status'))
        <div class="mb-4 p-3 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl">
            {{ session('status') }}
        </div>
    @endif

    <div class="form-card">
        <h3>Authentification à deux facteurs (par email)</h3>
        <p style="font-size:13px; color:#64748b; line-height:1.6; margin-bottom:16px;">
            Renforcez la sécurité de votre compte : en plus du mot de passe, un code à 6 chiffres
            vous sera envoyé par email (votre boîte Gmail par exemple) à chaque connexion.
            Aucune application d'authentification n'est nécessaire.
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
            <p style="font-size:13px; color:#64748b; line-height:1.6; margin-bottom:16px;">
                Après désactivation, un simple mot de passe suffira à vous connecter.
            </p>
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
                <li>Cliquez sur « Envoyer le code de vérification » ci-dessous.</li>
                <li>Le code sera envoyé à votre adresse email <strong>{{ $email }}</strong>.</li>
                <li>Saisissez le code reçu pour activer la protection.</li>
            </ol>

            <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; border-radius:12px; background:#EFF6FF; border:1px solid #BFDBFE; margin-bottom:18px;">
                <div style="flex-shrink:0; width:40px; height:40px; border-radius:10px; background:#0066FF; display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3"/>
                        <path d="M2 7l9.3 6a1 1 0 0 0 1.4 0L22 7"/>
                    </svg>
                </div>
                <div>
                    <strong style="font-size:13px; color:#0f172a;">Code envoyé à {{ $email }}</strong>
                    <div style="font-size:12px; color:#475569;">
                        Vérifiez votre boîte de réception (et les spams). Le code expire après 10 minutes.
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.two-factor.enable') }}" method="POST">
                @csrf
                <div class="form-field full">
                    <label>Code à 6 chiffres</label>
                    <input type="text" name="code" maxlength="6" inputmode="numeric" placeholder="••••••" required
                        style="letter-spacing:0.4em; font-weight:700; text-align:center;">
                    @error('code')
                        <span style="font-size:11px; color:#e11d48;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">Activer la 2FA</button>
                </div>
            </form>

            <form action="{{ route('profile.two-factor.resend') }}" method="POST" style="margin-top:14px;">
                @csrf
                <button type="submit" style="background:none; border:none; padding:0; font-size:12px; font-weight:600; color:#0066FF; cursor:pointer; text-decoration:underline;">
                    Je n'ai pas reçu le code — renvoyer
                </button>
            </form>
        </div>
    @endif
</div>

@endsection

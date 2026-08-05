@extends('components.email-layout')

@section('content')
    <div class="icon-badge icon-green">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 6l-10 7L2 6"/>
            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
        </svg>
    </div>

    <h2>Confirmez votre adresse email</h2>

    <p>
        Bienvenue sur Medix eSanté ! Pour activer votre compte et accéder à votre
        espace, cliquez sur le bouton ci-dessous. Le lien est valable
        <strong>60 minutes</strong>.
    </p>

    <div style="text-align:center; margin-bottom:24px;">
        <a href="{{ $url }}" style="display:inline-block; background:#0066FF; color:#ffffff; text-decoration:none; font-weight:700; font-size:15px; padding:14px 32px; border-radius:12px;">
            Vérifier mon adresse email
        </a>
    </div>

    <p class="expire-note">
        Si vous n'avez pas créé de compte sur Medix eSanté, vous pouvez ignorer
        cet email en toute sécurité.
    </p>
@endsection

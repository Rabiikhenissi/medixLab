@extends('components.email-layout')

@section('content')
    <div class="icon-badge icon-teal">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            <circle cx="12" cy="12" r="10"/>
        </svg>
    </div>

    <h2>Vous êtes invité(e) à rejoindre Medix eSanté</h2>

    <p>
        @if($invite->first_name)
            Bonjour {{ $invite->first_name }},
        @else
            Bonjour,
        @endif
        Un compte <strong>{{ $invite->roleLabel() }}</strong> vous a été préparé sur la
        plateforme Medix eSanté. Cliquez sur le bouton ci-dessous pour choisir votre mot de
        passe et activer votre compte. Le lien est valable
        <strong>{{ config('legal.invite_days') }} jours</strong>.
    </p>

    <div style="text-align:center; margin-bottom:24px;">
        <a href="{{ $url }}" class="btn btn-teal">
            Activer mon compte
        </a>
    </div>

    <p class="expire-note">
        Si vous n'attendiez pas cette invitation, vous pouvez ignorer cet email en toute
        sécurité — aucune action de votre part n'est requise.
    </p>
@endsection

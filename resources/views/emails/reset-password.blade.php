@extends('components.email-layout')

@section('content')
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
@endsection

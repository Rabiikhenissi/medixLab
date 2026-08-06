@extends('components.email-layout')

@section('content')
    <h2>{{ __('emails.reset.title') }}</h2>

    <p>
        {{ __('emails.reset.intro') }}<br>
        {{ __('emails.reset.instructions') }}
    </p>

    <a href="{{ $url }}" class="btn">{{ __('emails.reset.button') }}</a>

    <p>{{ __('emails.reset.link_fallback') }}&nbsp;:</p>
    <div class="url-box">{{ $url }}</div>

    <p class="expire-note">
        {{ __('emails.reset.expiry', ['minutes' => config('auth.passwords.users.expire', 60)]) }}<br>
        {{ __('emails.reset.ignore_note') }}
    </p>
@endsection

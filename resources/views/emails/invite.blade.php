@extends('components.email-layout')

@section('content')
    <div class="icon-badge icon-teal">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            <circle cx="12" cy="12" r="10"/>
        </svg>
    </div>

    <h2>{{ __('emails.invite.title') }}</h2>

    <p>
        @if($invite->first_name)
            {{ __('emails.invite.greeting', ['name' => $invite->first_name]) }}
        @else
            {{ __('emails.invite.greeting_no_name') }}
        @endif
        {!! __('emails.invite.intro', ['role' => $invite->roleLabel(), 'days' => config('legal.invite_days')]) !!}
    </p>

    <div style="text-align:center; margin-bottom:24px;">
        <a href="{{ $url }}" class="btn btn-teal">
            {{ __('emails.invite.button') }}
        </a>
    </div>

    <p class="expire-note">
        {{ __('emails.invite.expire_note') }}
    </p>
@endsection

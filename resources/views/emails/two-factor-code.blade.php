@extends('components.email-layout')

@section('content')
    <div class="icon-badge icon-blue">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
        </svg>
    </div>

    <h2>{{ __('emails.two_factor.title') }}</h2>

    <p>
        {{ __('emails.two_factor.intro') }}
        {{ __('emails.two_factor.use_code') }}
    </p>

    <div style="text-align:center; margin-bottom:24px;">
        <span style="display:inline-block; font-family:'Courier New', monospace; font-size:34px; font-weight:700; letter-spacing:10px; color:#0066FF; background:#EFF6FF; border:1px dashed #93C5FD; border-radius:14px; padding:16px 28px;">
            {{ $code }}
        </span>
    </div>

    <div class="info-box">
        <p>
            <strong>{{ __('emails.two_factor.valid_for', ['minutes' => $minutes]) }}</strong><br>
            <strong>{{ __('emails.date_label') }}</strong> {{ now()->format(__('common.datetime_format')) }}
        </p>
    </div>

    <p class="expire-note">
        {{ __('emails.two_factor.warning') }}
    </p>
@endsection

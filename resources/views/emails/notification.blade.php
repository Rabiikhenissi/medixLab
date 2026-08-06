@extends('components.email-layout')

@section('content')
    @php
        $iconMap = [
            'access_request' => ['icon' => 'key', 'color' => 'blue', 'label' => __('emails.notification.label_access_request')],
            'exam_request'   => ['icon' => 'flask', 'color' => 'teal', 'label' => __('emails.notification.label_exam_request')],
            'stock_alert'    => ['icon' => 'alert', 'color' => 'amber', 'label' => __('emails.notification.label_stock_alert')],
            'results_ready'  => ['icon' => 'check', 'color' => 'green', 'label' => __('emails.notification.label_results_ready')],
        ];
        $cfg = $iconMap[$type] ?? ['icon' => 'bell', 'color' => 'blue', 'label' => __('emails.notification.label_generic')];
    @endphp

    <div class="icon-badge icon-{{ $cfg['color'] }}">
        @if($cfg['icon'] === 'key')
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
            </svg>
        @elseif($cfg['icon'] === 'flask')
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 3h6m-5 0v7.5L4 18a1 1 0 0 0 .8 1.6h14.4A1 1 0 0 0 20 18l-6.5-7.5V3"/>
                <path d="M7 3h10"/>
            </svg>
        @elseif($cfg['icon'] === 'alert')
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        @elseif($cfg['icon'] === 'check')
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
        @endif
    </div>

    <h2>{{ $title }}</h2>

    <p>{!! nl2br(e($message)) !!}</p>

    <div class="info-box">
        <p>
            <strong>{{ __('emails.type_label') }}</strong> {{ $cfg['label'] }}<br>
            <strong>{{ __('emails.date_label') }}</strong> {{ now()->format(__('common.datetime_format')) }}
        </p>
    </div>

    <a href="{{ $actionUrl ?? config('app.url') }}" class="btn {{ $cfg['color'] === 'teal' ? 'btn-teal' : ($cfg['color'] === 'amber' ? 'btn-amber' : '') }}">
        {{ $actionLabel ?? __('emails.notification.open_space') }}
    </a>

    <p class="expire-note">
        {{ __('emails.notification.expire_note') }}
    </p>
@endsection

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

@section('title', __('profile.two_factor_meta'))

@section('page-title')
{{ __('profile.security') }} <span style="color:{{ $colors['primary'] }};">2FA</span>
@endsection

@section('page-subtitle')
{{ __('profile.two_factor_subtitle') }}
@endsection

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
    @if(session('success'))
        <div class="mb-5 p-4 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('status'))
        <div class="mb-5 p-4 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="glass-card rounded-2xl p-5 md:p-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0066FF] to-[#00A3FF] flex items-center justify-center shadow-md shadow-[#0066FF]/20">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-[#0f172a]">{{ __('profile.two_factor_heading') }}</h3>
                <p class="text-xs text-[#64748b]">{{ __('profile.two_factor_description') }}</p>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between p-4 rounded-xl
            {{ $enabled ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
            <div class="flex items-center gap-3">
                <span class="text-2xl">{{ $enabled ? '✅' : '⚠️' }}</span>
                <div>
                    <strong class="text-sm text-[#0f172a]">{{ $enabled ? __('profile.two_factor_enabled') : __('profile.two_factor_not_enabled') }}</strong>
                    @if($enabled)
                        <p class="text-xs text-[#475569] mt-0.5">{{ __('profile.two_factor_enabled_on', ['date' => auth()->user()->two_factor_confirmed_at?->format(__('common.datetime_format'))]) }}</p>
                    @else
                        <p class="text-xs text-[#475569] mt-0.5">{{ __('profile.two_factor_not_protected') }}</p>
                    @endif
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" {{ $enabled ? 'checked' : '' }} disabled>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0066FF]"></div>
            </label>
        </div>
    </div>

    @if($enabled)
        <div class="glass-card rounded-2xl p-5 md:p-8 mt-5 w-full">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#e11d48] to-[#be123c] flex items-center justify-center shadow-md shadow-[#e11d48]/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#0f172a]">{{ __('profile.disable_2fa_heading') }}</h3>
                    <p class="text-xs text-[#64748b]">{{ __('profile.disable_2fa_help') }}</p>
                </div>
            </div>

            <form action="{{ route('profile.two-factor.disable') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="disable-password" class="block text-xs font-bold text-[#475569] uppercase tracking-wider mb-2">{{ __('profile.current_password') }}</label>
                    <input id="disable-password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-white border-2 border-[#e2e8f0] rounded-xl px-4 py-3 text-sm text-[#1e293b] placeholder:text-[#CBD5E1] focus:outline-none focus:ring-4 focus:ring-[#e11d48]/10 focus:border-[#e11d48] transition-all duration-200"
                        placeholder="••••••••">
                    @error('password')
                        <span class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-[#e11d48] to-[#be123c] hover:from-[#be123c] hover:to-[#9f1239] text-white rounded-xl py-3 px-5 text-sm font-bold tracking-wide transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#e11d48]/20 cursor-pointer flex items-center justify-center gap-2 active:scale-[0.98] shadow-lg shadow-[#e11d48]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75M16.5 10.5l-4.5 4.5m0 0L10.5 10.5" />
                    </svg>
                    {{ __('profile.disable_2fa') }}
                </button>
            </form>
        </div>
    @else
        <div class="glass-card rounded-2xl p-5 md:p-8 mt-5 w-full">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0066FF] to-[#00A3FF] flex items-center justify-center shadow-md shadow-[#0066FF]/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-[#0f172a]">{{ __('profile.enable_2fa_heading') }}</h3>
                    <p class="text-xs text-[#64748b]">{{ __('profile.enable_2fa_help') }}</p>
                </div>
            </div>

            <div class="space-y-3 mb-5">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-[#F8FAFC] border border-[#e2e8f0]">
                    <span class="w-6 h-6 rounded-full bg-[#0066FF] text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                    <p class="text-sm text-[#475569]">{{ __('profile.enable_step1') }}</p>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-lg bg-[#F8FAFC] border border-[#e2e8f0]">
                    <span class="w-6 h-6 rounded-full bg-[#0066FF] text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                    <p class="text-sm text-[#475569]">{{ __('profile.enable_step2_prefix') }} <strong class="text-[#0f172a]">{{ $email }}</strong>.</p>
                </div>
                <div class="flex items-start gap-3 p-3 rounded-lg bg-[#F8FAFC] border border-[#e2e8f0]">
                    <span class="w-6 h-6 rounded-full bg-[#0066FF] text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                    <p class="text-sm text-[#475569]">{{ __('profile.enable_step3') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-xl bg-blue-50 border border-blue-200 mb-5">
                <div class="w-10 h-10 rounded-xl bg-[#0066FF] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="3"/>
                        <path d="M2 7l9.3 6a1 1 0 0 0 1.4 0L22 7"/>
                    </svg>
                </div>
                <div>
                    <strong class="text-sm text-[#0f172a]">{{ __('profile.code_sent_to') }} {{ $email }}</strong>
                    <p class="text-xs text-[#475569]">{{ __('profile.code_check_hint') }}</p>
                </div>
            </div>

            <form action="{{ route('profile.two-factor.enable') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="enable-code" class="block text-xs font-bold text-[#475569] uppercase tracking-wider mb-2">{{ __('profile.code_6_digits') }}</label>
                    <input id="enable-code" type="text" name="code" maxlength="6" inputmode="numeric" placeholder="• • • • • •" required
                        class="w-full text-center text-lg sm:text-xl font-bold tracking-[0.3em] sm:tracking-[0.4em] text-[#1e293b] placeholder:text-[#CBD5E1] bg-white border-2 border-[#e2e8f0] rounded-xl px-4 py-3 sm:px-5 sm:py-4 focus:outline-none focus:ring-4 focus:ring-[#0066FF]/10 focus:border-[#0066FF] transition-all duration-200">
                    @error('code')
                        <span class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-[#0066FF] to-[#0088FF] hover:from-[#0052CC] hover:to-[#0066CC] text-white rounded-xl py-3 px-5 text-sm font-bold tracking-wide transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#0066FF]/20 cursor-pointer flex items-center justify-center gap-2 active:scale-[0.98] shadow-lg shadow-[#0066FF]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('profile.enable_2fa') }}
                </button>
            </form>

            <form action="{{ route('profile.two-factor.resend') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-white border-2 border-[#e2e8f0] hover:border-[#0066FF] hover:bg-[#F0F7FF] text-[#0066FF] rounded-xl py-3 px-5 text-sm font-bold tracking-wide transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#0066FF]/20 cursor-pointer flex items-center justify-center gap-2 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                    </svg>
                    {{ __('profile.resend_code') }}
                </button>
            </form>
        </div>
    @endif
</div>

@endsection

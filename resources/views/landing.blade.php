@php
    $pageTitle = __('landing.meta_title');
    $pageDescription = __('seo.landing_description');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Medix eSanté">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">

    <!-- Google Fonts: Outfit & Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8FAFC] text-[#1e293b] antialiased min-h-screen relative overflow-x-hidden font-sans">
    <x-background-animation />

    <div class="relative z-10">
        <!-- ══ HEADER ══ -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-white/70 border-b border-[#e2e8f0]/70">
            <div class="max-w-6xl mx-auto px-4 md:px-6 py-3 flex items-center justify-between gap-4">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 select-none">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0066FF] to-[#00aaff] flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.315 48.315 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                    </span>
                    <span class="text-lg font-extrabold tracking-tight text-[#0f172a]">Medix <span class="text-[#0066FF]">eSanté</span></span>
                </a>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:block"><x-language-switcher /></div>
                    <a href="{{ route('home') }}"
                        class="hidden md:inline-flex text-sm font-semibold text-[#475569] hover:text-[#0066FF] transition-colors px-3 py-2">
                        {{ __('landing.nav_login') }}
                    </a>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#0066FF] text-white text-sm font-bold shadow-lg shadow-blue-500/25 hover:bg-[#0052CC] transition-colors">
                        {{ __('landing.nav_register') }}
                    </a>
                </div>
            </div>
        </header>

        <!-- ══ HERO ══ -->
        <section class="relative py-16 md:py-24">
            <div class="max-w-6xl mx-auto px-4 md:px-6 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0066FF]/10 text-[#0066FF] text-xs font-bold uppercase tracking-widest mb-5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ __('landing.hero_badge') }}
                    </span>
                    <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-[#1e293b] leading-tight mb-6">
                        {{ __('landing.hero_title_1') }}<br>
                        <span class="text-[#0066FF]">{{ __('landing.hero_title_2') }}</span>
                    </h1>
                    <p class="text-base md:text-lg text-[#64748b] max-w-xl mb-9 font-medium leading-relaxed">
                        {{ __('landing.hero_subtitle') }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-[#0066FF] text-white text-base font-bold shadow-lg shadow-blue-500/30 hover:bg-[#0052CC] hover:-translate-y-0.5 transition-all duration-200">
                            {{ __('landing.cta_primary') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#features"
                            class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full border border-[#e2e8f0] bg-white text-[#475569] text-base font-semibold hover:border-[#0066FF] hover:text-[#0066FF] transition-colors">
                            {{ __('landing.cta_secondary') }}
                        </a>
                    </div>
                </div>

                <!-- Hero visual: mock dashboard -->
                <div class="relative hidden lg:block select-none">
                    <div class="glass-card rounded-[24px] p-6 shadow-2xl shadow-blue-500/10">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0066FF] to-[#00aaff]"></span>
                                <div>
                                    <p class="text-xs font-bold text-[#1e293b]">{{ __('landing.hero_title_1') }}</p>
                                    <p class="text-[10px] text-[#94a3b8]">Medix eSanté</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100/80 text-emerald-700 text-[10px] font-bold uppercase">{{ __('landing.hero_visual_safe') }}</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-xl bg-white border border-[#e2e8f0] p-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg bg-sky-100/80 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-[#1e293b]">{{ __('landing.hero_visual_result') }}</p>
                                        <p class="text-[11px] text-[#94a3b8]">{{ __('landing.hero_visual_result_date') }}</p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-[#0D9488]">{{ __('landing.hero_visual_ready') }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-white border border-[#e2e8f0] p-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg bg-violet-100/80 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-[#1e293b]">{{ __('landing.hero_visual_message') }}</p>
                                        <p class="text-[11px] text-[#94a3b8]">{{ __('landing.hero_visual_message_preview') }}</p>
                                    </div>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-[#0066FF]"></span>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-white border border-[#e2e8f0] p-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg bg-emerald-100/80 flex items-center justify-center">
                                        <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-[#1e293b]">{{ __('landing.hero_visual_document') }}</p>
                                        <p class="text-[11px] text-[#94a3b8]">{{ __('landing.hero_visual_document_sub') }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ AUDIENCE ══ -->
        <section class="py-14 md:py-20">
            <div class="max-w-6xl mx-auto px-4 md:px-6">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-[#1e293b] mb-3">{{ __('landing.audience_title') }}</h2>
                    <p class="text-sm md:text-base text-[#64748b] max-w-2xl mx-auto">{{ __('landing.audience_subtitle') }}</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    <a href="{{ route('patient.register') }}" class="glass-card rounded-[20px] p-7 hover:-translate-y-1 hover:shadow-xl transition-all duration-200 group">
                        <span class="w-12 h-12 rounded-xl bg-sky-100/80 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-bold text-[#1e293b] mb-2 group-hover:text-[#0066FF] transition-colors">{{ __('landing.audience_patient') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.audience_patient_desc') }}</p>
                    </a>
                    <a href="{{ route('doctor.register') }}" class="glass-card rounded-[20px] p-7 hover:-translate-y-1 hover:shadow-xl transition-all duration-200 group">
                        <span class="w-12 h-12 rounded-xl bg-emerald-100/80 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v6m0 0a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V4m-6 8h12M6 12a6 6 0 0012 0" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-bold text-[#1e293b] mb-2 group-hover:text-[#0066FF] transition-colors">{{ __('landing.audience_doctor') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.audience_doctor_desc') }}</p>
                    </a>
                    <a href="{{ route('center.register') }}" class="glass-card rounded-[20px] p-7 hover:-translate-y-1 hover:shadow-xl transition-all duration-200 group">
                        <span class="w-12 h-12 rounded-xl bg-rose-100/80 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-bold text-[#1e293b] mb-2 group-hover:text-[#0066FF] transition-colors">{{ __('landing.audience_lab') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.audience_lab_desc') }}</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- ══ FEATURES ══ -->
        <section id="features" class="py-14 md:py-20 bg-white/60 border-y border-[#e2e8f0]/70">
            <div class="max-w-6xl mx-auto px-4 md:px-6">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-[#1e293b] mb-3">{{ __('landing.features_title') }}</h2>
                    <p class="text-sm md:text-base text-[#64748b] max-w-2xl mx-auto">{{ __('landing.features_subtitle') }}</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-sky-100/80 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_results_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_results_desc') }}</p>
                    </div>
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-violet-100/80 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_history_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_history_desc') }}</p>
                    </div>
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-emerald-100/80 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_chat_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_chat_desc') }}</p>
                    </div>
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-cyan-100/80 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4V2m0 20v-2m8-8h2M2 12h2m13.657-5.657l1.414-1.414M4.929 4.929l1.414 1.414m13.314 8.486l1.414 1.414M4.929 19.071l1.414-1.414M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_qr_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_qr_desc') }}</p>
                    </div>
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-amber-100/80 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_prescription_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_prescription_desc') }}</p>
                    </div>
                    <div class="rounded-[20px] border border-[#e2e8f0] bg-white p-7">
                        <span class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center mb-4">
                            <svg class="w-5.5 h-5.5 text-[#475569]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <h3 class="font-bold text-[#1e293b] mb-1.5">{{ __('landing.feature_security_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed">{{ __('landing.feature_security_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ HOW IT WORKS ══ -->
        <section class="py-14 md:py-20">
            <div class="max-w-6xl mx-auto px-4 md:px-6">
                <div class="text-center mb-12">
                    <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-[#1e293b] mb-3">{{ __('landing.steps_title') }}</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <span class="w-12 h-12 rounded-full bg-[#0066FF] text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-500/30">1</span>
                        <h3 class="font-bold text-[#1e293b] mb-2">{{ __('landing.step1_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed max-w-xs mx-auto">{{ __('landing.step1_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <span class="w-12 h-12 rounded-full bg-[#0066FF] text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-500/30">2</span>
                        <h3 class="font-bold text-[#1e293b] mb-2">{{ __('landing.step2_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed max-w-xs mx-auto">{{ __('landing.step2_desc') }}</p>
                    </div>
                    <div class="text-center">
                        <span class="w-12 h-12 rounded-full bg-[#0066FF] text-white font-extrabold text-lg flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-500/30">3</span>
                        <h3 class="font-bold text-[#1e293b] mb-2">{{ __('landing.step3_title') }}</h3>
                        <p class="text-sm text-[#64748b] leading-relaxed max-w-xs mx-auto">{{ __('landing.step3_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ CTA BAND ══ -->
        <section class="pb-16 md:pb-24 px-4 md:px-6">
            <div class="max-w-6xl mx-auto rounded-[28px] bg-gradient-to-br from-[#0066FF] to-[#00aaff] px-6 md:px-12 py-12 md:py-16 text-center shadow-2xl shadow-blue-500/25">
                <h2 class="text-2xl md:text-4xl font-extrabold tracking-tight text-white mb-3">{{ __('landing.cta_band_title') }}</h2>
                <p class="text-sm md:text-base text-white/85 max-w-xl mx-auto mb-8">{{ __('landing.cta_band_subtitle') }}</p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-white text-[#0066FF] text-base font-bold shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    {{ __('landing.cta_band_button') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </section>

        <!-- ══ FOOTER ══ -->
        <footer class="border-t border-[#e2e8f0]/70 bg-white/60 py-10">
            <div class="max-w-6xl mx-auto px-4 md:px-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <p class="text-sm font-bold text-[#1e293b] mb-1">Medix <span class="text-[#0066FF]">eSanté</span></p>
                    <p class="text-xs text-[#94a3b8]">{{ __('landing.footer_tagline') }}</p>
                </div>
                <nav class="flex items-center gap-6 text-sm">
                    <a href="{{ route('legal.terms') }}" class="text-[#64748b] hover:text-[#0066FF] transition-colors">{{ __('legal.terms.title') }}</a>
                    <a href="{{ route('legal.privacy') }}" class="text-[#64748b] hover:text-[#0066FF] transition-colors">{{ __('legal.privacy.title') }}</a>
                    <a href="{{ route('legal.mentions') }}" class="text-[#64748b] hover:text-[#0066FF] transition-colors">{{ __('legal.mentions.title') }}</a>
                </nav>
            </div>
        </footer>
    </div>
</body>

</html>

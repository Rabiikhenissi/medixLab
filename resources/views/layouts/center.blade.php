<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Espace Etablissement') - Medix eSanté</title>

        <!-- Google Fonts: Outfit & Instrument Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F8FAFC] text-[#1e293b] antialiased min-h-screen relative overflow-x-hidden font-sans">
        <!-- Reusable Background Animation -->
        <x-background-animation />

        <!-- Main Wrapper -->
        <div class="relative z-10 min-h-screen flex flex-col items-center p-4 md:p-8">
            <div class="w-full max-w-[1000px] mx-auto py-4">
                <div class="glass-card rounded-[24px] p-6 md:p-8 relative overflow-hidden">
                    
                    <!-- Alert Success / Error inside layout -->
                    @if (session('success'))
                        <div class="flex items-center justify-between bg-[#f0fdf4] border border-[#bbf7d0] rounded-xl p-4 mb-6 text-sm text-[#166534] font-medium" id="center-success-alert">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-[#16a34a]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button onclick="document.getElementById('center-success-alert').remove()" class="text-[#94a3b8] hover:text-[#475569] transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flex items-center justify-between bg-[#fff1f2] border border-[#fecaca] rounded-xl p-4 mb-6 text-sm text-[#dc2626] font-medium" id="center-error-alert">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-[#ef4444]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button onclick="document.getElementById('center-error-alert').remove()" class="text-[#cbd5e1] hover:text-[#94a3b8] transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-5 mb-6 select-none">
                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#7C3AED] to-[#5B21B6] flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 20V8l8 7 8-7v12" />
                                    <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="white" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-[#1e293b] leading-tight">{{ auth()->user()->staff->laboratory->name }}</h2>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold tracking-wider text-[#7C3AED] bg-[#7C3AED]/10 border border-[#7C3AED]/20 uppercase">
                                        Etablissement
                                    </span>
                                    <span class="text-xs text-[#64748b] font-medium">Responsable : {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <form action="{{ route('center.logout') }}" method="POST">
                                @csrf
                                <x-button type="submit" color="slate" :fullWidth="false" class="!py-2 !px-4 !text-xs font-bold uppercase tracking-wider">
                                    SE DÉCONNECTER
                                </x-button>
                            </form>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
@php
    $route = request()->route()->getName();
@endphp

<div class="flex flex-wrap gap-2 border-b border-[#e2e8f0]/50 pb-4 mb-6">

    <!-- Dashboard -->
    <a href="{{ route('center.dashboard') }}"
       class="flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 
       {{ $route === 'center.dashboard' ? 'bg-[#7C3AED] text-white shadow-md shadow-[#7C3AED]/20' : 'text-[#64748b] hover:text-[#1e293b] hover:bg-[#F1F5F9]' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/>
        </svg>

        <span>Tableau de Bord</span>
    </a>


    <!-- Working Hours -->
    <a href="{{ route('center.working-hours') }}"
       class="flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 
       {{ str_starts_with($route, 'center.working-hours') ? 'bg-[#7C3AED] text-white shadow-md shadow-[#7C3AED]/20' : 'text-[#64748b] hover:text-[#1e293b] hover:bg-[#F1F5F9]' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <span>Horaires</span>
    </a>


    <!-- Consumables -->
    <a href="{{ route('center.consumables') }}"
       class="flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 
       {{ str_starts_with($route, 'center.consumables') ? 'bg-[#7C3AED] text-white shadow-md shadow-[#7C3AED]/20' : 'text-[#64748b] hover:text-[#1e293b] hover:bg-[#F1F5F9]' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M9.75 3.104v13.01m0-13.01L6 6.854m3.75-3.75l3.75 3.75M3 21h18"/>
        </svg>

        <span>Stock & Consommables</span>
    </a>


    <!-- Equipment -->
    <a href="{{ route('center.equipment') }}"
       class="flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 
       {{ str_starts_with($route, 'center.equipment') ? 'bg-[#7C3AED] text-white shadow-md shadow-[#7C3AED]/20' : 'text-[#64748b] hover:text-[#1e293b] hover:bg-[#F1F5F9]' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877"/>
        </svg>

        <span>Equipements & Maintenance</span>
    </a>


    <!-- Exam Requests -->
    <a href="{{ route('center.exam-requests') }}"
       class="flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition duration-200 
       {{ str_starts_with($route, 'center.exam-requests') ? 'bg-[#7C3AED] text-white shadow-md shadow-[#7C3AED]/20' : 'text-[#64748b] hover:text-[#1e293b] hover:bg-[#F1F5F9]' }}">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
        </svg>

        <span>Demandes d'analyses</span>
    </a>

</div>

                    <!-- Page Content -->
                    <div class="mt-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        @yield('scripts')
    </body>
</html>

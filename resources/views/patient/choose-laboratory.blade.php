<x-layouts.patient>
<x-slot:title>Choisir un laboratoire — Medix eSanté</x-slot:title>

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .lab-card {
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .lab-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(13,148,136,0.12);
    }
    .lab-card.selected-card {
        border-color: #0D9488;
        box-shadow: 0 0 0 2px rgba(13,148,136,0.35);
    }
    .city-bubble {
        transition: all 0.15s ease;
    }
    .city-bubble:hover { transform: scale(1.05); }
    .city-bubble.active { transform: scale(1.05); }
    .badge-pulse {
        animation: pulse-ring 2s ease-in-out infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .sort-btn {
        transition: all 0.15s ease;
    }
    .sort-btn.active {
        background: #0D9488;
        color: white;
        border-color: #0D9488;
        box-shadow: 0 2px 8px rgba(13,148,136,0.25);
    }
    .rec-badge {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .price-highlight {
        background: linear-gradient(135deg, #0D9488, #14b8a6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endsection

@section('content')

@php
    $labsWithCoords = $laboratories->filter(fn($lab) => $lab->latitude && $lab->longitude);
@endphp

<div class="w-full max-w-[900px] mx-auto py-8 px-4">

    {{-- Back button --}}
    <a href="{{ route('patient.dashboard') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#0D9488] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour au tableau de board
    </a>

    {{-- Map section --}}
    @if($labsWithCoords->count() > 0)
    <div class="mb-6 rounded-2xl overflow-hidden border border-[#e2e8f0] shadow-xs" style="height: 320px;">
        <div id="labMap" style="width:100%;height:100%;"></div>
    </div>
    @endif

    <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">

        {{-- Decorative gradient blob --}}
        <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-gradient-to-br from-[#0D9488]/10 to-purple-500/10 blur-3xl pointer-events-none"></div>

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-[#0D9488]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#1e293b]">Choisir un laboratoire</h1>
                <p class="text-xs text-[#64748b] mt-0.5">Sélectionnez le laboratoire qui effectuera vos analyses</p>
            </div>
        </div>

        {{-- Prescription Info bar --}}
        <div class="mt-5 mb-7 p-4 bg-[#0D9488]/5 border border-[#0D9488]/20 rounded-xl flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3 flex-1">
                <svg class="w-5 h-5 text-[#0D9488] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-[#1e293b]">
                    Prescription <span class="font-bold">#{{ $examRequest->id }}</span>
                    @if($examRequest->laboratory)
                        · Laboratoire actuel : <strong class="text-[#0D9488]">{{ $examRequest->laboratory->name }}</strong>
                    @endif
                </p>
            </div>
            <button type="button" onclick="loadSplitSuggestions()"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition cursor-pointer whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Répartir entre labos
            </button>
        </div>

        {{-- Search + Sort + Compat filter + Count --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="labSearchInput" placeholder="Rechercher un laboratoire..."
                    class="w-full pl-9 pr-4 py-2.5 text-sm rounded-xl border border-[#e2e8f0] bg-white focus:outline-none focus:ring-2 focus:ring-[#0D9488]/30 focus:border-[#0D9488] transition text-[#1e293b]">
            </div>
            <div class="flex items-center gap-3">
                @if(count($requiredExamIds) > 0)
                <label class="flex items-center gap-2 cursor-pointer select-none" title="N'afficher que les labs couvrant tous vos examens">
                    <div class="relative">
                        <input type="checkbox" id="compatFilter" class="sr-only peer">
                        <div class="w-9 h-5 bg-[#e2e8f0] rounded-full peer-checked:bg-[#0D9488] transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                    </div>
                    <span class="text-xs font-bold text-[#475569] whitespace-nowrap">Labs compatibles</span>
                </label>
                @endif
                <span id="labResultCount" class="text-sm text-[#94a3b8] font-medium whitespace-nowrap">
                    {{ count($laboratories) }} laboratoire(s)
                </span>
            </div>
        </div>

        {{-- Sort controls --}}
        <div class="flex items-center gap-2 mb-5 flex-wrap">
            <span class="text-[10px] font-bold text-[#94a3b8] uppercase tracking-wider">Trier par :</span>
            <button type="button" data-sort="recommended" class="sort-btn active inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#e2e8f0] bg-white text-[#64748b] transition">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Recommandé
            </button>
            <button type="button" data-sort="price" class="sort-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#e2e8f0] bg-white text-[#64748b] transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Prix
            </button>
            <button type="button" data-sort="distance" class="sort-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#e2e8f0] bg-white text-[#64748b] transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Distance
            </button>
            <button type="button" data-sort="compat" class="sort-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border border-[#e2e8f0] bg-white text-[#64748b] transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Compatibilité
            </button>
        </div>

        {{-- City bubble filters --}}
        @php
            $cities = $laboratories->pluck('city')->filter()->unique()->sort()->values();
        @endphp
        @if($cities->count() > 1)
        <div class="flex flex-wrap gap-2 mb-6" id="cityBubbles">
            <button type="button" data-city="all"
                class="city-bubble active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border transition bg-[#0D9488] text-white border-[#0D9488] shadow-sm">
                Toutes les villes
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-black bg-white/25">{{ count($laboratories) }}</span>
            </button>
            @foreach($cities as $city)
            <button type="button" data-city="{{ $city }}"
                class="city-bubble inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border transition text-[#64748b] bg-white border-[#e2e8f0] hover:border-[#0D9488]/40 hover:text-[#0D9488]">
                📍 {{ $city }}
                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-black bg-black/10">
                    {{ $laboratories->where('city', $city)->count() }}
                </span>
            </button>
            @endforeach
        </div>
        @endif

        {{-- Lab grid --}}
        <div id="labGrid" class="grid md:grid-cols-2 gap-4">
            @foreach($rankedLabs as $entry)
            @php
                $lab = $entry['lab'];
                $scores = $entry['scores'];
                $rec = $entry['recommendation'];
                $availability = $availabilityMap[$lab->id] ?? ['status' => 'unknown', 'label' => '', 'color' => 'gray'];

                $totalPrice = $scores['total_price'];
                $isFullyCompatible = $scores['is_fully_compatible'];
                $coveredCount = $scores['covered_count'];
                $totalRequired = count($requiredExamIds);
                $compatPct = $totalRequired > 0 ? round($coveredCount / $totalRequired * 100) : 100;

                $sortPrice = $totalPrice > 0 ? $totalPrice : 9999;
                $sortCompat = $compatPct;
            @endphp
            <div class="lab-card bg-white border border-[#e2e8f0] rounded-2xl p-5 flex flex-col"
                data-name="{{ strtolower($lab->name) }}"
                data-city="{{ strtolower($lab->city ?? '') }}"
                data-city-exact="{{ $lab->city ?? '' }}"
                data-compat="{{ $isFullyCompatible ? '1' : '0' }}"
                data-lab-id="{{ $lab->id }}"
                data-score="{{ $entry['total_score'] }}"
                data-price="{{ $sortPrice }}"
                data-distance="{{ $scores['distance'] }}"
                data-compat-pct="{{ $sortCompat }}">

                {{-- Recommendation badges --}}
                @if($rec)
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($rec as $badge)
                    <span class="rec-badge inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                        @if($badge['color'] === 'emerald') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @elseif($badge['color'] === 'green') bg-green-50 text-green-700 border border-green-200
                        @elseif($badge['color'] === 'blue') bg-blue-50 text-blue-700 border border-blue-200
                        @elseif($badge['color'] === 'amber') bg-amber-50 text-amber-700 border border-amber-200
                        @elseif($badge['color'] === 'indigo') bg-indigo-50 text-indigo-700 border border-indigo-200
                        @else bg-slate-50 text-slate-700 border border-slate-200
                        @endif">
                        @if($badge['icon'] === 'star')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @elseif($badge['icon'] === 'clock')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($badge['icon'] === 'map')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @elseif($badge['icon'] === 'check')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($badge['icon'] === 'price')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        @endif
                        {{ $badge['text'] }}
                    </span>
                    @endforeach
                </div>
                @endif

                {{-- Lab Header --}}
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0D9488]/10 flex items-center justify-center flex-shrink-0 text-[#0D9488] font-black text-base">
                        {{ strtoupper(substr($lab->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-[#1e293b] text-sm leading-tight">{{ $lab->name }}</h2>
                            {{-- Availability dot --}}
                            <span class="badge-pulse inline-block w-2 h-2 rounded-full flex-shrink-0
                                @if($availability['status'] === 'open') bg-green-500 shadow-[0_0_6px_rgba(34,197,94,0.5)]
                                @elseif($availability['status'] === 'closing_soon') bg-amber-400 shadow-[0_0_6px_rgba(251,191,36,0.5)]
                                @elseif($availability['status'] === 'opens_soon') bg-amber-300
                                @else bg-red-400
                                @endif"
                                title="{{ $availability['label'] }}"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            @if($lab->city)
                            <span class="inline-flex items-center gap-1 text-xs text-[#0D9488] font-medium">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                {{ $lab->city }}
                            </span>
                            @endif
                            <span class="text-[10px] font-medium {{ $availability['color'] === 'green' ? 'text-green-600' : ($availability['color'] === 'amber' ? 'text-amber-600' : 'text-red-500') }}">
                                {{ $availability['label'] }}
                            </span>
                        </div>
                        {{-- Exam compatibility bar --}}
                        @if ($totalRequired > 0)
                        <div class="mt-2 flex items-center gap-1.5">
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $isFullyCompatible ? 'bg-green-500' : ($coveredCount > 0 ? 'bg-amber-400' : 'bg-red-400') }} transition-all"
                                     style="width: {{ $compatPct }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold whitespace-nowrap {{ $isFullyCompatible ? 'text-green-600' : ($coveredCount > 0 ? 'text-amber-600' : 'text-red-500') }}">
                                {{ $coveredCount }}/{{ $totalRequired }} examens
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Contact info --}}
                <div class="space-y-1.5 mb-4 text-xs text-[#64748b]">
                    @if($lab->address)
                    <div class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-[#94a3b8] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $lab->address }}</span>
                    </div>
                    @endif
                    @if($lab->phone)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-[#94a3b8] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ $lab->phone }}" class="hover:text-[#0D9488] transition">{{ $lab->phone }}</a>
                    </div>
                    @endif
                    @if($lab->email)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-[#94a3b8] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $lab->email }}" class="hover:text-[#0D9488] transition truncate">{{ $lab->email }}</a>
                    </div>
                    @endif
                </div>

                {{-- Total price for this prescription --}}
                @if($totalRequired > 0 && $totalPrice > 0)
                <div class="mb-4 p-3 bg-gradient-to-r from-[#0D9488]/5 to-teal-50 rounded-xl border border-[#0D9488]/10">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Total prescription</span>
                        <span class="price-highlight text-lg font-black">{{ number_format($totalPrice, 2) }} <span class="text-xs font-bold">TND</span></span>
                    </div>
                </div>
                @endif

                {{-- Working Hours (collapsible) --}}
                @if($lab->workingHours->count() > 0)
                <details class="mb-4 group">
                    <summary class="text-xs font-bold text-[#1e293b] uppercase tracking-wider cursor-pointer flex items-center gap-1.5 list-none select-none">
                        <svg class="w-3.5 h-3.5 text-[#0D9488] transition-transform group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                        Horaires & Jours de Repos
                    </summary>
                    <div class="mt-2 space-y-1 pl-5">
                        <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider mb-1">Horaires habituels :</p>
                        @foreach($lab->workingHours->whereNull('date_close') as $hours)
                        <p class="text-xs text-[#64748b] flex justify-between">
                            <span class="font-medium text-[#475569]">{{ $hours->day }}</span>
                            <span>
                                @if($hours->is_closed)
                                    <span class="text-red-500 font-semibold">Fermé</span>
                                @else
                                    {{ substr($hours->start_time, 0, 5) }} – {{ substr($hours->end_time, 0, 5) }}
                                @endif
                            </span>
                        </p>
                        @endforeach

                        @php
                            $exceptions = $lab->workingHours->whereNotNull('date_close')->sortBy('date_close');
                        @endphp
                        @if($exceptions->count() > 0)
                        <div class="mt-3 pt-2.5 border-t border-[#e2e8f0]/60">
                            <p class="text-[10px] font-bold text-[#dc2626] uppercase tracking-wider mb-1.5">Fermetures / Repos exceptionnels :</p>
                            <div class="space-y-1">
                                @foreach($exceptions as $exc)
                                <p class="text-xs text-[#dc2626] flex justify-between items-center bg-red-50/50 px-2.5 py-1.5 rounded-lg border border-red-100/50">
                                    <span class="font-semibold">{{ $exc->date_close->format('d/m/Y') }}</span>
                                    <span class="text-[10px] italic text-[#7f1d1d] font-medium max-w-[150px] truncate" title="{{ $exc->day }}">{{ $exc->day }}</span>
                                </p>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </details>
                @endif

                {{-- Available exams & prices --}}
                @php
                    $matchingExams = $lab->availableExams
                        ->where('is_active', true)
                        ->filter(fn($ae) => in_array($ae->exam_id, $requiredExamIds));
                @endphp
                @if($matchingExams->count() > 0)
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-[#1e293b] uppercase tracking-wider mb-1.5">Examens disponibles pour cette prescription :</p>
                    <div class="space-y-1">
                        @foreach($matchingExams as $ae)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#475569] font-medium flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                {{ $ae->exam->name }}
                            </span>
                            @if($ae->price)
                            <span class="font-bold text-[#0D9488]">{{ number_format($ae->price, 2) }} TND</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Spacer --}}
                <div class="flex-1"></div>

                {{-- Map button --}}
                <button type="button" onclick="openLabOnMap({{ $lab->id }})"
                    class="w-full flex items-center justify-center gap-2 mb-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold py-2 rounded-xl transition-all duration-200 text-xs border border-blue-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Voir sur la carte
                </button>

                {{-- Action button --}}
                <form method="POST" action="{{ route('patient.assign-laboratory', $examRequest) }}">
                    @csrf
                    <input type="hidden" name="labo_id" value="{{ $lab->id }}">
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-[#0D9488] hover:bg-[#0a7068] active:scale-[0.98] text-white font-bold py-2.5 rounded-xl transition-all duration-200 uppercase tracking-wider text-xs shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Choisir ce laboratoire
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div id="labEmpty" class="hidden py-16 text-center text-[#94a3b8]">
            <svg class="w-14 h-14 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-sm font-semibold">Aucun laboratoire trouvé</p>
            <p class="text-xs mt-1">Essayez une autre ville ou effacez la recherche</p>
        </div>

        {{-- Pagination --}}
        <div id="labPagination" class="flex items-center justify-between mt-6 pt-5 border-t border-[#e2e8f0]/60 hidden">
            <p id="labPageInfo" class="text-xs text-[#94a3b8] font-medium"></p>
            <div class="flex items-center gap-1" id="labPageButtons"></div>
        </div>

    </div>
</div>

<script>
    const LAB_PAGE_SIZE  = 6;
    let labCurrentPage   = 1;
    let activeCityFilter = 'all';
    let searchTerm       = '';
    let compatOnly       = false;
    let currentSort      = 'recommended';

    const allCards = Array.from(document.querySelectorAll('.lab-card'));
    const labGrid  = document.getElementById('labGrid');
    const labEmpty = document.getElementById('labEmpty');
    const labPag   = document.getElementById('labPagination');
    const labInfo  = document.getElementById('labPageInfo');
    const labBtns  = document.getElementById('labPageButtons');
    const labCount = document.getElementById('labResultCount');

    // Compatibility toggle
    const compatToggle = document.getElementById('compatFilter');
    if (compatToggle) {
        compatToggle.addEventListener('change', () => {
            compatOnly     = compatToggle.checked;
            labCurrentPage = 1;
            render();
        });
    }

    // Sort controls
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentSort = btn.dataset.sort;
            labCurrentPage = 1;
            sortCards();
            render();
        });
    });

    function sortCards() {
        const sortKey = {
            'recommended': 'score',
            'price': 'price',
            'distance': 'distance',
            'compat': 'compatPct'
        }[currentSort];

        const sortAsc = currentSort === 'price';

        allCards.sort((a, b) => {
            const aVal = parseFloat(a.dataset[sortKey]) || 0;
            const bVal = parseFloat(b.dataset[sortKey]) || 0;
            return sortAsc ? aVal - bVal : bVal - aVal;
        });

        allCards.forEach(card => labGrid.appendChild(card));
    }

    function getVisible() {
        return allCards.filter(card => {
            const nameMatch   = card.dataset.name.includes(searchTerm);
            const cityMatch   = activeCityFilter === 'all' || card.dataset.cityExact === activeCityFilter;
            const compatMatch = !compatOnly || card.dataset.compat === '1';
            return nameMatch && cityMatch && compatMatch;
        });
    }

    function render() {
        const visible    = getVisible();
        const total      = visible.length;
        const totalPages = Math.max(1, Math.ceil(total / LAB_PAGE_SIZE));
        if (labCurrentPage > totalPages) labCurrentPage = totalPages;

        const start = (labCurrentPage - 1) * LAB_PAGE_SIZE;
        const end   = Math.min(labCurrentPage * LAB_PAGE_SIZE, total);

        allCards.forEach(c => c.classList.add('hidden'));
        visible.slice(start, end).forEach(c => c.classList.remove('hidden'));

        labCount.textContent = `${total} laboratoire(s)`;

        labEmpty.classList.toggle('hidden', total > 0);
        labGrid.classList.toggle('hidden', total === 0);

        if (totalPages <= 1) {
            labPag.classList.add('hidden');
        } else {
            labPag.classList.remove('hidden');
            labInfo.textContent = `${start + 1}–${end} sur ${total}`;

            const pages = [];
            for (let i = Math.max(1, labCurrentPage - 2); i <= Math.min(totalPages, labCurrentPage + 2); i++) pages.push(i);

            labBtns.innerHTML = `
                <button type="button" id="labPrevBtn" ${labCurrentPage === 1 ? 'disabled' : ''}
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all
                           ${labCurrentPage === 1 ? 'text-[#cbd5e1] cursor-not-allowed' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                ${pages.map(p => `
                    <button type="button" data-p="${p}"
                        class="lab-page-btn w-8 h-8 rounded-full text-xs font-bold transition-all
                               ${p === labCurrentPage ? 'bg-[#0D9488] text-white shadow-sm' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">
                        ${p}
                    </button>`).join('')}
                <button type="button" id="labNextBtn" ${labCurrentPage === totalPages ? 'disabled' : ''}
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all
                           ${labCurrentPage === totalPages ? 'text-[#cbd5e1] cursor-not-allowed' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>`;

            document.getElementById('labPrevBtn')?.addEventListener('click', () => { if (labCurrentPage > 1) { labCurrentPage--; render(); scrollToTop(); } });
            document.getElementById('labNextBtn')?.addEventListener('click', () => { if (labCurrentPage < totalPages) { labCurrentPage++; render(); scrollToTop(); } });
            document.querySelectorAll('.lab-page-btn').forEach(btn => {
                btn.addEventListener('click', () => { labCurrentPage = parseInt(btn.dataset.p); render(); scrollToTop(); });
            });
        }
    }

    function scrollToTop() {
        document.getElementById('labGrid').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // City bubbles
    document.querySelectorAll('.city-bubble').forEach(btn => {
        btn.addEventListener('click', () => {
            activeCityFilter = btn.dataset.city;
            labCurrentPage   = 1;
            document.querySelectorAll('.city-bubble').forEach(b => {
                const isActive = b.dataset.city === activeCityFilter;
                b.classList.toggle('active', isActive);
                if (isActive) {
                    b.className = b.className.replace(/text-\[#64748b\]|bg-white|border-\[#e2e8f0\]|hover:border-\[#0D9488\]\/40|hover:text-\[#0D9488\]/g, '').trim();
                    b.classList.add('bg-[#0D9488]', 'text-white', 'border-[#0D9488]', 'shadow-sm');
                } else {
                    b.classList.remove('bg-[#0D9488]', 'text-white', 'border-[#0D9488]', 'shadow-sm');
                    b.classList.add('text-[#64748b]', 'bg-white', 'border-[#e2e8f0]');
                }
            });
            render();
        });
    });

    // Search input
    document.getElementById('labSearchInput').addEventListener('input', e => {
        searchTerm     = e.target.value.toLowerCase().trim();
        labCurrentPage = 1;
        render();
    });

    // Initial sort and render
    sortCards();
    render();
</script>

{{-- Split Modal --}}
<div id="splitModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-[1000] flex items-center justify-center p-4">
    <div class="bg-white rounded-[20px] w-full max-w-lg p-6 md:p-8 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5 pb-4 border-b border-[#e2e8f0]">
            <div>
                <h3 class="text-base font-bold text-[#1e293b]">Répartition entre laboratoires</h3>
                <p class="text-[11px] text-[#64748b] mt-0.5">Optimisation pour couvrir tous vos examens</p>
            </div>
            <button onclick="closeSplitModal()" class="w-8 h-8 rounded-full bg-[#f1f5f9] hover:bg-[#e2e8f0] flex items-center justify-center text-[#64748b] hover:text-[#1e293b] transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="splitContent">
            <div class="text-center py-8">
                <div class="animate-spin w-6 h-6 border-2 border-[#0D9488] border-t-transparent rounded-full mx-auto mb-3"></div>
                <p class="text-xs text-[#64748b]">Analyse des laboratoires disponibles...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    window.openLabOnMap = function() {
        Swal.fire({ icon: 'info', title: 'Localisation', text: 'Chargement de la carte en cours...', confirmButtonColor: '#0D9488' });
    };
</script>
@if($labsWithCoords->count() > 0)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function() {
        var labs = {!! json_encode($labsWithCoords->map(fn($lab) => [
            'id' => $lab->id,
            'name' => $lab->name,
            'city' => $lab->city ?? '',
            'address' => $lab->address ?? '',
            'lat' => (float)$lab->latitude,
            'lng' => (float)$lab->longitude,
        ])->values()->toArray()) !!};

        if (labs.length === 0) return;

        var mapEl = document.getElementById('labMap');
        if (!mapEl) return;

        var map = L.map('labMap').setView([labs[0].lat, labs[0].lng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var markers = {};
        var allMarkers = [];
        labs.forEach(function(lab) {
            var marker = L.marker([lab.lat, lab.lng]).addTo(map)
                .bindPopup('<div style="font-family:Inter,sans-serif"><b>' + lab.name + '</b><br><span style="font-size:11px;color:#64748b">' + (lab.address ? lab.address + '<br>' : '') + lab.city + '</span></div>');
            markers[lab.id] = marker;
            allMarkers.push(marker);
        });

        if (labs.length > 1) {
            var group = L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        setTimeout(function() { map.invalidateSize(); }, 200);

        window.openLabOnMap = function(labId) {
            if (markers[labId]) {
                document.getElementById('labMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(function() {
                    map.invalidateSize();
                    map.setView(markers[labId].getLatLng(), 15);
                    markers[labId].openPopup();
                }, 400);
            } else {
                Swal.fire({ icon: 'info', title: 'Localisation', text: 'Ce laboratoire n\'a pas de position géographique enregistrée.', confirmButtonColor: '#0D9488' });
            }
        };
    })();
</script>
@endif

<script>
    const examRequestId = {{ $examRequest->id }};
    const splitUrl = '{{ route("patient.split-suggestions", $examRequest) }}';
    const applySplitUrl = '{{ route("patient.apply-split", $examRequest) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const allExams = {!! json_encode($examRequest->items->map(fn($it) => ['id' => $it->exam_id, 'name' => $it->exam->name])->values()->toArray()) !!};

    async function loadSplitSuggestions() {
        const modal = document.getElementById('splitModal');
        const content = document.getElementById('splitContent');
        modal.classList.remove('hidden');
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin w-6 h-6 border-2 border-[#0D9488] border-t-transparent rounded-full mx-auto mb-3"></div>
                <p class="text-xs text-[#64748b]">Analyse des laboratoires disponibles...</p>
            </div>`;

        try {
            const res = await fetch(splitUrl);
            const data = await res.json();
            if (!data.success || data.split.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="text-sm font-semibold text-[#1e293b]">Un seul laboratoire suffit</p>
                        <p class="text-xs text-[#94a3b8] mt-1">Tous vos examens sont couverts par au moins un labo</p>
                    </div>`;
                return;
            }
            renderSplit(data.split);
        } catch(e) {
            content.innerHTML = '<p class="text-xs text-red-500 text-center py-4">Erreur de chargement</p>';
        }
    }

    let splitData = [];

    function renderSplit(split) {
        const content = document.getElementById('splitContent');
        splitData = split;
        const uncovered = split.filter(s => s.uncovered);
        const covered = split.filter(s => !s.uncovered);

        let html = '';

        if (covered.length > 1) {
            html += `<div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-xl">
                <p class="text-[11px] font-bold text-purple-700">${covered.length} laboratoire(s) nécessaires pour couvrir tous vos examens</p>
            </div>`;
        }

        covered.forEach((group, i) => {
            const checkedId = 'split-check-' + i;
            html += `
            <div class="split-lab-card mb-4 p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-[#0D9488]/40 ${group.is_primary ? 'bg-[#0D9488]/5 border-[#0D9488]/30' : 'bg-white border-[#e2e8f0]'}"
                 onclick="toggleSplitCheck(${i})">
                <input type="checkbox" id="${checkedId}" class="split-lab-check sr-only" data-index="${i}" ${group.uncovered ? 'disabled' : 'checked'}>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <div id="split-cb-${i}" class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all flex-shrink-0
                            ${!group.uncovered ? 'border-[#0D9488] bg-[#0D9488]' : 'border-[#cbd5e1] bg-white'}">
                            <svg class="w-3 h-3 text-white ${!group.uncovered ? '' : 'hidden'}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="w-7 h-7 rounded-full ${group.is_primary ? 'bg-[#0D9488] text-white' : 'bg-purple-100 text-purple-700'} flex items-center justify-center text-[11px] font-black flex-shrink-0">${i + 1}</span>
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-[#1e293b] truncate">${group.lab_name}</div>
                            ${group.is_primary ? '<span class="inline-block mt-0.5 text-[9px] font-bold text-[#0D9488] bg-[#0D9488]/10 px-1.5 py-0.5 rounded">PRINCIPAL</span>' : ''}
                        </div>
                    </div>
                    ${group.total_price > 0 ? `<span class="text-sm font-black text-[#0D9488] flex-shrink-0 ml-2">${Number(group.total_price).toFixed(2)} TND</span>` : ''}
                </div>
                <div class="flex items-center gap-1.5 text-[10px] text-[#64748b] mb-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    ${group.exam_ids.length} examen(s) couverts
                </div>
                <div id="splitExamList_${i}" class="hidden pt-2 border-t border-[#e2e8f0]/60">
                    ${renderExamTags(group)}
                </div>
                <button type="button" onclick="event.stopPropagation(); toggleExamList(${i})" class="mt-1 text-[10px] font-bold text-[#0D9488] hover:text-[#0a7068] transition cursor-pointer">
                    Voir les examens ▾
                </button>
            </div>`;
        });

        if (uncovered.length > 0) {
            html += `
            <div class="p-4 border-2 border-dashed border-red-300 rounded-xl bg-red-50 mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-sm font-bold text-red-700">Examen(s) non couvert(s)</span>
                </div>
                <p class="text-[11px] text-red-600">${uncovered[0].exam_ids.length} examen(s) sans laboratoire disponible</p>
            </div>`;
        }

        html += `<div class="flex gap-3 pt-4 border-t border-[#e2e8f0]">
            <button onclick="closeSplitModal()" class="flex-1 py-3 rounded-xl bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold text-xs uppercase tracking-wider transition cursor-pointer">Annuler</button>
            <button onclick="applySplit()" id="applySplitBtn" class="flex-1 py-3 rounded-xl bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold text-xs uppercase tracking-wider transition cursor-pointer shadow-md shadow-teal-200">Appliquer la répartition</button>
        </div>`;

        content.innerHTML = html;
        updateApplyBtn();
    }

    function toggleSplitCheck(index) {
        const cb = document.getElementById('split-check-' + index);
        if (cb.disabled) return;
        cb.checked = !cb.checked;
        const card = cb.closest('.split-lab-card');
        const visual = document.getElementById('split-cb-' + index);
        const svg = visual.querySelector('svg');
        if (cb.checked) {
            card.className = card.className.replace('border-[#e2e8f0] bg-white', 'border-[#0D9488]/30 bg-[#0D9488]/5');
            visual.className = visual.className.replace('border-[#cbd5e1] bg-white', 'border-[#0D9488] bg-[#0D9488]');
            svg.classList.remove('hidden');
        } else {
            card.className = card.className.replace('border-[#0D9488]/30 bg-[#0D9488]/5', 'border-[#e2e8f0] bg-white');
            visual.className = visual.className.replace('border-[#0D9488] bg-[#0D9488]', 'border-[#cbd5e1] bg-white');
            svg.classList.add('hidden');
        }
        updateApplyBtn();
    }

    function renderExamTags(group) {
        return `<div class="flex flex-wrap gap-1">${group.exam_ids.map(eid => {
            const exam = allExams.find(e => e.id === eid);
            const name = exam ? exam.name : 'Examen #' + eid;
            return `<span class="px-2 py-0.5 bg-[#0D9488]/10 text-[#0D9488] text-[9px] font-bold rounded-full border border-[#0D9488]/15">${name}</span>`;
        }).join('')}</div>`;
    }

    function toggleExamList(index) {
        const el = document.getElementById('splitExamList_' + index);
        el.classList.toggle('hidden');
    }

    function updateApplyBtn() {
        const checked = document.querySelectorAll('.split-lab-check:checked:not(:disabled)');
        const btn = document.getElementById('applySplitBtn');
        if (btn) btn.disabled = checked.length === 0;
    }

    document.addEventListener('change', e => {
        if (e.target.classList.contains('split-lab-check')) {
            updateApplyBtn();
        }
    });

    async function applySplit() {
        const checked = document.querySelectorAll('.split-lab-check:checked:not(:disabled)');
        if (checked.length === 0) return;

        const selectedIndices = [...checked].map(cb => parseInt(cb.dataset.index));
        const assignments = selectedIndices.map(idx => ({
            labo_id: splitData[idx].labo_id,
            exam_ids: splitData[idx].exam_ids,
        }));

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = applySplitUrl;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        assignments.forEach((a, i) => {
            const laboInput = document.createElement('input');
            laboInput.type = 'hidden';
            laboInput.name = `assignments[${i}][labo_id]`;
            laboInput.value = a.labo_id;
            form.appendChild(laboInput);

            a.exam_ids.forEach((eid) => {
                const examInput = document.createElement('input');
                examInput.type = 'hidden';
                examInput.name = `assignments[${i}][exam_ids][]`;
                examInput.value = eid;
                form.appendChild(examInput);
            });
        });

        document.body.appendChild(form);
        form.submit();
    }

    function closeSplitModal() {
        document.getElementById('splitModal').classList.add('hidden');
    }
</script>
@endsection

</x-layouts.patient>

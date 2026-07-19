<x-layouts.auth>
<x-slot:title>Choisir un laboratoire — Medix eSanté</x-slot:title>

<style>
    .lab-card {
        transition: transform 0.18s ease, box-shadow 0.18s ease;
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
</style>

<div class="w-full max-w-[900px] mx-auto py-8 px-4">

    {{-- Back button --}}
    <a href="{{ route('patient.dashboard') }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#64748b] hover:text-[#0D9488] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour au tableau de bord
    </a>

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
        <div class="mt-5 mb-7 p-4 bg-[#0D9488]/5 border border-[#0D9488]/20 rounded-xl flex items-center gap-3">
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

        {{-- Search + Compat filter + Count --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
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
                    <span class="text-xs font-bold text-[#475569] whitespace-nowrap">Labs compatibles uniquement</span>
                </label>
                @endif
                <span id="labResultCount" class="text-sm text-[#94a3b8] font-medium whitespace-nowrap">
                    {{ count($laboratories) }} laboratoire(s)
                </span>
            </div>
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
            @foreach($laboratories as $lab)
            @php
                $labAvailableExamIds = $lab->availableExams->where('is_active', true)->pluck('exam_id')->toArray();
                $coveredCount        = count(array_intersect($requiredExamIds, $labAvailableExamIds));
                $totalRequired       = count($requiredExamIds);
                $isFullyCompatible   = $totalRequired === 0 || $coveredCount === $totalRequired;
                $compatPct           = $totalRequired > 0 ? round($coveredCount / $totalRequired * 100) : 100;
            @endphp
            <div class="lab-card bg-white border border-[#e2e8f0] rounded-2xl p-5 flex flex-col"
                data-name="{{ strtolower($lab->name) }}"
                data-city="{{ strtolower($lab->city ?? '') }}"
                data-city-exact="{{ $lab->city ?? '' }}"
                data-compat="{{ $isFullyCompatible ? '1' : '0' }}">

                {{-- Lab Header --}}
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0D9488]/10 flex items-center justify-center flex-shrink-0 text-[#0D9488] font-black text-base">
                        {{ strtoupper(substr($lab->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-bold text-[#1e293b] text-sm leading-tight">{{ $lab->name }}</h2>
                        @if($lab->city)
                        <span class="inline-flex items-center gap-1 mt-1 text-xs text-[#0D9488] font-medium">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                            {{ $lab->city }}
                        </span>
                        @endif
                        {{-- Exam compatibility badge (Task 3.4) --}}
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

                {{-- Available exams & prices (Task 3.4) --}}
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
                            <span class="font-bold text-[#0D9488]">{{ number_format($ae->price, 2) }} DZD</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Spacer --}}
                <div class="flex-1"></div>

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

        {{-- Empty state (hidden by default) --}}
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

        // Hide all
        allCards.forEach(c => c.classList.add('hidden'));
        // Show page slice
        visible.slice(start, end).forEach(c => c.classList.remove('hidden'));

        // Count
        labCount.textContent = `${total} laboratoire(s)`;

        // Empty
        labEmpty.classList.toggle('hidden', total > 0);
        labGrid.classList.toggle('hidden', total === 0);

        // Pagination
        if (totalPages <= 1) {
            labPag.classList.add('hidden');
        } else {
            labPag.classList.remove('hidden');
            labInfo.textContent = `${start + 1}–${end} sur ${total}`;

            // Build page buttons
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

    // Initial render
    render();
</script>

</x-layouts.auth>
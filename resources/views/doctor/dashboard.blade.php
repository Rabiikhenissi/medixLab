<x-layouts.doctor>
    <x-slot:title>Tableau de bord Médecin - Medix eSanté</x-slot:title>

    @section('content')
    <div class="w-full max-w-7xl mx-auto">

        {{-- ── Header Bar ── --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-4 select-none">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#1e293b]">
                        Dr. <span class="text-[#0066FF]">{{ $user->first_name }} {{ $user->last_name }}</span>
                    </h1>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0066FF] bg-[#0066FF]/10 border border-[#0066FF]/20 uppercase mt-1">
                        Espace Médecin Connecté
                    </span>
                </div>
            </div>

            {{-- Top Right Patient Search --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <form id="headerPatientSearchForm" class="flex items-center gap-2">
                    @csrf
                    <div class="relative flex-1">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#0066FF]" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="header_patient_code" placeholder="Rechercher patient par code..."
                            class="w-full sm:w-60 pl-10 pr-4 py-2.5 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] text-xs bg-white shadow-xs font-semibold"
                            autocomplete="off" required />
                    </div>
                    <button type="submit"
                        class="bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold px-4 py-2.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md text-xs uppercase tracking-wider flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6" />
                        </svg>
                        Rechercher
                    </button>
                    <button type="button" id="openQrScannerBtn"
                        class="bg-white border border-[#e2e8f0] hover:border-[#0066FF]/40 hover:bg-[#EFF6FF]/40 text-[#0066FF] font-bold w-10 h-10 rounded-xl transition flex items-center justify-center shadow-xs cursor-pointer flex-shrink-0"
                        title="Scanner un QR Code patient">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </button>
                </form>

                {{-- Notification Bell --}}
                <div class="relative" id="doctorNotifWrapper">
                    <button onclick="doctorToggleNotifPanel()" class="relative w-10 h-10 bg-white border border-[#e2e8f0] rounded-xl flex items-center justify-center hover:bg-[#f8fafc] transition cursor-pointer">
                        <svg class="w-5 h-5 text-[#64748b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span id="doctorNotifBadge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">0</span>
                    </button>
                    <div id="doctorNotifPanel" class="hidden fixed top-20 right-4 md:absolute md:top-12 md:right-0 w-80 max-w-[calc(100vw-2rem)] bg-white border border-[#e2e8f0] rounded-2xl shadow-2xl z-50 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-[#f1f5f9]">
                            <span class="text-xs font-bold text-[#1e293b] uppercase tracking-wider">Notifications</span>
                            <button onclick="doctorMarkAllRead()" class="text-[10px] font-bold text-[#0066FF] hover:underline cursor-pointer">Tout marquer lu</button>
                        </div>
                        <div id="doctorNotifList" class="max-h-72 overflow-y-auto divide-y divide-[#f1f5f9]">
                            <div class="px-4 py-6 text-center text-xs text-[#94a3b8]">Chargement...</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.show') }}" class="w-10 h-10 bg-gradient-to-br from-[#0066FF] to-[#0052CC] rounded-xl flex items-center justify-center text-white text-xs font-bold hover:shadow-lg transition cursor-pointer" title="Mon profil">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                </a>

                <form action="{{ route('doctor.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] hover:text-[#1e293b] font-bold px-4 py-2.5 rounded-xl transition text-xs uppercase tracking-wider border border-[#e2e8f0] flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Main Grid ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left & Center Column (2/3 width) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Statistics Dashboard Cards --}}
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('doctor.exam-groups.index') }}"
                        class="relative overflow-hidden p-6 bg-gradient-to-br from-[#0066FF]/5 to-[#0066FF]/10 border border-[#0066FF]/20 rounded-2xl shadow-xs block hover:border-[#0066FF]/40 transition">
                        <div class="text-3xl font-black text-[#0066FF] mb-1">{{ $doctorStats['examGroupsCount'] }}
                        </div>
                        <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider">Groupes d'Examens</p>
                        <svg class="absolute -right-3 -bottom-3 w-14 h-14 text-[#0066FF]/10" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                        </svg>
                    </a>
                    <div
                        class="relative overflow-hidden p-6 bg-gradient-to-br from-emerald-500/5 to-emerald-500/10 border border-emerald-500/20 rounded-2xl shadow-xs">
                        <div class="text-3xl font-black text-emerald-600 mb-1">
                            {{ $doctorStats['examRequestsCount'] }}</div>
                        <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider">Prescriptions Envoyées
                        </p>
                        <svg class="absolute -right-3 -bottom-3 w-14 h-14 text-emerald-500/10" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                {{-- ── Statistics & Analytics ── --}}
                <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden shadow-xs">
                    <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-widest mb-5 flex items-center gap-2 pb-4 border-b border-[#e2e8f0]/80">
                        <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Statistiques & Analytiques
                    </h3>

                    {{-- Stat Cards --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-[#0066FF]/5 to-[#0066FF]/10 border border-[#0066FF]/20 rounded-2xl p-5 relative overflow-hidden">
                            <div class="text-3xl font-black text-[#0066FF] mb-1">{{ $uniquePatientsCount }}</div>
                            <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider">Patients Uniques</p>
                            <svg class="absolute -right-3 -bottom-3 w-14 h-14 text-[#0066FF]/10" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-500/5 to-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 relative overflow-hidden">
                            <div class="text-3xl font-black text-emerald-600 mb-1">{{ $completionRate }}%</div>
                            <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider">Taux Complétion</p>
                            <svg class="absolute -right-3 -bottom-3 w-14 h-14 text-emerald-500/10" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Monthly Prescriptions Bar Chart --}}
                    <div class="bg-white border border-[#e2e8f0]/80 rounded-2xl p-5">
                        <p class="text-[11px] font-bold text-[#64748b] uppercase tracking-wider mb-4">Prescriptions Mensuelles (6 derniers mois)</p>
                        <div style="height: 220px;">
                            <canvas id="doctorMonthlyChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Previous Exams Given by Doctor --}}
                <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden shadow-xs">
                    <h3
                        class="text-xs font-bold text-[#64748b] uppercase tracking-widest mb-5 flex items-center gap-2 pb-4 border-b border-[#e2e8f0]/80">
                        <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                        Dernières Prescriptions d'Analyses
                    </h3>
                    {{-- Search & Filter Form --}}
                    <form action="{{ route('doctor.dashboard') }}" method="GET" class="mb-5 flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative flex-1 w-full">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom de patient ou code..." 
                                   class="w-full pl-4 pr-10 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] bg-white shadow-xs" />
                            @if($search)
                                <a href="{{ route('doctor.dashboard', ['status' => $status]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                        <div class="w-full sm:w-48">
                            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] bg-white text-[#1e293b] cursor-pointer shadow-xs">
                                <option value="all" {{ $status == 'all' || !$status ? 'selected' : '' }}>Tous les statuts</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="collected" {{ $status == 'collected' ? 'selected' : '' }}>Collecté</option>
                                <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>En cours</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider transition">Filtrer</button>
                    </form>

                    @if($recentExams->count() > 0)
                        <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
                            @foreach($recentExams as $examReq)
                                                    <div
                                                        class="bg-white border border-[#e2e8f0]/80 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs hover:border-[#0066FF]/25 transition">
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <p class="font-bold text-sm text-[#1e293b]">
                                                                    {{ $examReq->patient->user->first_name }}
                                                                    {{ $examReq->patient->user->last_name }}
                                                                </p>
                                                                <span
                                                                    class="text-[10px] text-[#64748b] font-mono bg-[#f1f5f9] px-2 py-0.5 rounded border border-[#e2e8f0]">
                                                                    {{ $examReq->patient->patient_code }}
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-[11px] text-[#64748b]">
                                                                <span class="flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5 text-[#0066FF]" fill="none" stroke="currentColor"
                                                                        stroke-width="2" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                    {{ $examReq->created_at->format('d/m/Y à H:i') }}
                                                                </span>
                                                                <span class="flex items-center gap-1 font-semibold text-[#0066FF]">
                                                                    {{ $examReq->items->count() }} examen(s)
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2 self-start sm:self-auto">
                                                            @php
                                                                $statusColors = [
                                                                    'pending' => 'text-amber-700 bg-amber-50 border-amber-200',
                                                                    'collected' => 'text-blue-700 bg-blue-50 border-blue-200',
                                                                    'processing' => 'text-purple-700 bg-purple-50 border-purple-200',
                                                                    'completed' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                                                                    'cancelled' => 'text-red-700 bg-red-50 border-red-200',
                                                                ];
                                                                $statusLabels = [
                                                                    'pending' => 'En attente',
                                                                    'collected' => 'Collecté',
                                                                    'processing' => 'Traitement',
                                                                    'completed' => 'Complété',
                                                                    'cancelled' => 'Annulé',
                                                                ];
                                                                $colorClass = $statusColors[$examReq->status] ?? 'text-[#64748b] bg-[#f1f5f9] border-[#e2e8f0]';
                                                                $label = $statusLabels[$examReq->status] ?? $examReq->status;
                                                             @endphp
                                                            <span
                                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $colorClass }}">
                                                                {{ $label }}
                                                            </span>

                                                            <button type="button"
                                                                class="inline-flex items-center gap-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold px-3 py-1.5 rounded-lg border border-[#e2e8f0] text-xs uppercase tracking-wider cursor-pointer viewRequestBtn"
                                                                data-id="{{ $examReq->id }}"
                                                                data-patient="{{ $examReq->patient->user->first_name }} {{ $examReq->patient->user->last_name }}"
                                                                data-date="{{ $examReq->created_at->format('d/m/Y H:i') }}"
                                                                data-notes="{{ $examReq->clinical_notes ?? 'Aucune note clinique' }}"
                                                                data-status="{{ $examReq->status }}"
                                                                data-approved-by-doctor="{{ $examReq->approved_by_doctor ? '1' : '0' }}"
                                                                data-doctor-interpretation="{{ $examReq->doctor_interpretation }}"
                                                                data-items-results="{{ json_encode($examReq->items->map(function ($it) {
                                         return ['exam_name' => $it->exam->name, 'result' => $it->resultLabo ? ['interpretation' => $it->resultLabo->interpretation, 'details' => $it->resultLabo->details] : null]; })) }}"
                                                                data-exams="{{ json_encode($examReq->items->map(fn($it) => $it->exam->name)) }}">
                                                                Voir Détails
                                                            </button>
                                                        </div>
                                                    </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $recentExams->links() }}
                        </div>
                    @else
                        <div class="text-center py-10 border border-dashed border-[#cbd5e1] rounded-xl">
                            <p class="text-xs text-[#94a3b8] italic">Aucune prescription ne correspond aux critères.</p>
                        </div>
                    @endif
                </div>

                {{-- Groupes d'Examens CRUD Section --}}
                <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden shadow-xs">
                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-[#e2e8f0]/80">
                        <h3 class="text-xs font-bold text-[#64748b] uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Mes Groupes d'Examens
                        </h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('doctor.exam-groups.create') }}"
                                class="bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold px-3 py-1.5 rounded-xl transition text-[10px] uppercase tracking-wider flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Nouveau
                            </a>
                            <a href="{{ route('doctor.exam-groups.index') }}"
                                class="bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold px-3 py-1.5 rounded-xl transition text-[10px] uppercase tracking-wider flex items-center gap-1 border border-[#e2e8f0]">
                                Gérer
                            </a>
                        </div>
                    </div>

                    @if($doctorGroups->count() > 0)
                        <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
                            @foreach($doctorGroups as $group)
                                <div class="bg-white border border-[#e2e8f0]/80 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs hover:border-[#0066FF]/25 transition group-card-item"
                                    data-id="{{ $group->id }}">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-sm text-[#1e293b] group-name-label">
                                                {{ $group->name }}
                                            </p>
                                            <span
                                                class="text-[9px] text-[#0066FF] font-bold bg-[#0066FF]/10 px-2 py-0.5 rounded border border-[#0066FF]/20">
                                                {{ $group->items->count() }} examen(s)
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-[#64748b] truncate mt-1 group-desc-label">
                                            {{ $group->description ?? 'Aucune description' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5 self-start sm:self-auto flex-shrink-0">
                                        <a href="{{ route('doctor.exam-groups.edit', $group->id) }}"
                                            class="inline-flex items-center gap-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold px-2.5 py-1.5 rounded-lg border border-[#e2e8f0] text-[10px] uppercase tracking-wider">
                                            Voir
                                        </a>
                                        <a href="{{ route('doctor.exam-groups.edit', $group->id) }}"
                                            class="inline-flex items-center gap-1 bg-[#EFF6FF] hover:bg-[#DBEAFE] text-[#0066FF] font-bold px-2.5 py-1.5 rounded-lg border border-[#BFDBFE] text-[10px] uppercase tracking-wider">
                                            Modifier
                                        </a>
                                        <form action="{{ route('doctor.exam-groups.destroy', $group->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return swalConfirmSubmit(this, 'Supprimer définitivement « {{ addslashes($group->name) }} » ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2.5 py-1.5 rounded-lg border border-red-100 text-[10px] uppercase tracking-wider cursor-pointer">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="text-center py-10 border border-dashed border-[#cbd5e1] rounded-xl no-groups-placeholder">
                            <p class="text-xs text-[#94a3b8] italic mb-3">Vous n'avez pas encore créé de groupe d'examens
                                personnalisé.</p>
                            <a href="{{ route('doctor.exam-groups.create') }}"
                                class="inline-flex items-center gap-1 bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold px-4 py-2 rounded-xl text-[10px] uppercase tracking-wider">
                                Créer un groupe
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Doctor Profile Info Summary --}}
                <div class="glass-card rounded-[20px] p-6 relative overflow-hidden shadow-xs">
                    <h3
                        class="text-xs font-bold text-[#64748b] uppercase tracking-widest mb-4 flex items-center gap-2 pb-3 border-b border-[#e2e8f0]/80">
                        <svg class="w-4 h-4 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informations Professionnelles
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-[#1e293b]">
                        <div
                            class="flex items-center justify-between p-3 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl">
                            <span class="text-[#64748b] font-medium">CNOM :</span>
                            <span class="font-bold font-mono text-[#0066FF]">{{ $user->doctor->doctor_code }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl">
                            <span class="text-[#64748b] font-medium">Spécialité :</span>
                            <span class="font-bold capitalize">{{ $user->doctor->speciality }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column (1/3 width) - Tab/Panel of Previous Patients --}}
            <div class="lg:col-span-1">
                <div class="glass-card rounded-[20px] p-6 md:p-8 relative overflow-hidden shadow-xs">

                    {{-- Title block --}}
                    <div class="flex items-center gap-2.5 mb-6 pb-4 border-b border-[#e2e8f0]/80">
                        <div
                            class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0066FF]/15 to-[#0066FF]/5 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider">Patients Récents</h3>
                            <p class="text-[11px] text-[#64748b]">Accès autorisé précédemment</p>
                        </div>
                    </div>

                    {{-- Patients List --}}
                    @if($recentPatients->count() > 0)
                        <div class="space-y-3 max-h-[480px] overflow-y-auto pr-1">
                            @foreach($recentPatients as $access)
                                <div
                                    class="bg-gradient-to-br from-[#F8FAFC] to-[#EFF6FF]/40 border border-[#e2e8f0] rounded-xl p-4 hover:border-[#0066FF]/35 transition shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm">
                                            {{ strtoupper(substr($access->patient->user->first_name, 0, 1) . substr($access->patient->user->last_name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-xs text-[#1e293b] truncate">
                                                {{ $access->patient->user->first_name }} {{ $access->patient->user->last_name }}
                                            </p>
                                            <p
                                                class="text-[9px] text-[#64748b] font-mono bg-white px-1.5 py-0.5 rounded border border-[#e2e8f0] inline-block mt-1">
                                                {{ $access->patient->patient_code }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3.5 pt-3 border-t border-[#e2e8f0]/60 flex gap-2">
                                        <a href="{{ route('doctor.select-exams', $access->patient->id) }}"
                                            class="flex-1 bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-2 px-3 rounded-lg text-[10px] uppercase tracking-wider text-center transition transform hover:scale-[1.02] active:scale-[0.98] shadow-xs cursor-pointer block">
                                            Prescrire des Examens
                                        </a>
                                        <a href="{{ route('doctor.chat', $access->patient->id) }}"
                                            class="inline-flex items-center justify-center gap-1 bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2 px-3 rounded-lg text-[10px] uppercase tracking-wider transition transform hover:scale-[1.02] active:scale-[0.98] shadow-xs cursor-pointer"
                                            title="Envoyer un message">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-[#F8FAFC]/50 border border-dashed border-[#e2e8f0] rounded-xl">
                            <p class="text-xs text-[#94a3b8] italic">Aucun patient avec accès actuellement.</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>

    <!-- Details Modal for Previous Exams -->
    <div id="requestDetailsModal"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-[600px] w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#e2e8f0]/80">
                    <h3 class="text-base font-bold text-[#1e293b]">Détails de la Prescription</h3>
                    <button type="button"
                        class="closeDetailsModalBtn text-[#94a3b8] hover:text-[#1e293b] transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-[#64748b] font-medium block">Patient :</span>
                        <span id="modalPatientName" class="font-bold text-sm text-[#1e293b]"></span>
                    </div>
                    <div>
                        <span class="text-[#64748b] font-medium block">Date :</span>
                        <span id="modalDate" class="font-semibold text-[#1e293b]"></span>
                    </div>
                    <div>
                        <span class="text-[#64748b] font-medium block">Notes cliniques :</span>
                        <div id="modalNotes"
                            class="p-3 bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl text-[#64748b] leading-relaxed italic mt-1">
                        </div>
                    </div>
                    <div>
                        <span class="text-[#64748b] font-medium block mb-2">Examens prescrits :</span>
                        <div id="modalExamsList" class="space-y-1.5 max-h-[180px] overflow-y-auto pr-1"></div>
                    </div>

                    {{-- Lab Results Section --}}
                    <div id="modalLabResultsSection" class="hidden border-t border-[#e2e8f0]/80 pt-4 space-y-3">
                        <span class="text-[#64748b] font-bold text-xs uppercase tracking-wider block">Résultats du
                            Laboratoire :</span>
                        <div id="modalLabResultsContainer" class="space-y-4"></div>
                    </div>

                    {{-- Doctor Interpretation Section --}}
                    <div id="modalDoctorInterpretationSection"
                        class="hidden border-t border-[#e2e8f0]/80 pt-4 space-y-3">
                        <span class="text-[#64748b] font-bold text-xs uppercase tracking-wider block">Interprétation &
                            Validation Médicale :</span>

                        <div id="doctorInterpretationRead"
                            class="hidden p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 leading-relaxed font-semibold">
                        </div>

                        <form id="doctorInterpretationForm" method="POST" action="" class="hidden space-y-3">
                            @csrf
                            <textarea id="modalDoctorInterpretationInput" name="doctor_interpretation" rows="4"
                                class="w-full p-4 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] text-xs leading-relaxed"
                                placeholder="Saisissez votre interprétation médicale pour finaliser l'examen..."
                                required></textarea>
                            <button type="submit"
                                class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-3 rounded-xl transition transform hover:scale-[1.01] active:scale-[0.99] shadow-md uppercase tracking-wider text-xs cursor-pointer text-center">
                                Soumettre l'interprétation et valider
                            </button>
                        </form>
                    </div>
                </div>

                <div class="pt-6 border-t border-[#e2e8f0]/80 mt-6 flex justify-end gap-3">
                    <button type="button" id="modalDoctorPrintBtn"
                        class="hidden bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 px-5 rounded-xl transition uppercase tracking-wider text-xs cursor-pointer">
                        🖨 Imprimer Résultats
                    </button>
                    <button type="button"
                        class="closeDetailsModalBtn bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold py-2.5 px-5 rounded-xl transition border border-[#e2e8f0] uppercase tracking-wider text-xs cursor-pointer">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results / Access Action Modal (Triggered by Header Search) -->
    <div id="searchResultModal"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-[460px] w-full shadow-2xl">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#e2e8f0]/80">
                    <h3 class="text-base font-bold text-[#1e293b]">Patient Trouvé</h3>
                    <button type="button"
                        class="closeSearchModalBtn text-[#94a3b8] hover:text-[#1e293b] transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Patient Card Details inside Modal -->
                <div class="bg-gradient-to-br from-[#F8FAFC] to-[#EFF6FF] border border-[#e2e8f0] rounded-2xl p-5 mb-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-full bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-md">
                            <span id="searchAvatar">P</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="searchName" class="text-sm font-bold text-[#1e293b] truncate">—</h4>
                            <p id="searchCodeBadge"
                                class="text-[9px] text-[#0066FF] font-mono bg-[#0066FF]/10 px-2 py-0.5 rounded border border-[#0066FF]/20 inline-block mt-1">
                                —</p>
                            <p id="searchEmail" class="text-[11px] text-[#64748b] truncate mt-1"></p>
                            <p id="searchPhone" class="text-[11px] text-[#64748b]"></p>
                        </div>
                    </div>
                </div>

                <!-- Action Button logic -->
                <div class="space-y-2.5">
                    <button type="button" id="requestAccessBtn"
                        class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md flex items-center justify-center gap-2 uppercase tracking-wider text-xs cursor-pointer">
                        Demander l'Accès au Patient
                    </button>

                    <button type="button" id="proceedToExamsBtn"
                        class="hidden w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md flex items-center justify-center gap-2 uppercase tracking-wider text-xs cursor-pointer">
                        Prescrire des Examens
                    </button>

                    <div id="accessGrantedMessage"
                        class="hidden p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-bold text-emerald-800">Accès autorisé ✓</p>
                                <p class="text-emerald-700 mt-0.5">L'accès à ce patient est accordé. Vous pouvez
                                    prescrire.</p>
                            </div>
                        </div>
                    </div>

                    <div id="accessPendingMessage"
                        class="hidden p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 animate-spin flex-shrink-0" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-bold text-amber-800">Demande en attente</p>
                                <p class="text-amber-700 mt-0.5">Le patient doit confirmer votre demande sur son compte.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="closeSearchModalBtn w-full bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold py-2.5 px-4 rounded-xl transition border border-[#e2e8f0] uppercase tracking-wider text-xs cursor-pointer text-center">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Modal -->
    <div id="qrScannerOverlay" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] w-full max-w-md overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#e2e8f0]/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#0066FF]/15 to-[#0066FF]/5 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#1e293b]">Scanner QR Code Patient</h3>
                </div>
                <button id="closeQrScannerBtn" class="text-[#94a3b8] hover:text-[#1e293b] transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div id="qrReaderRegion" class="rounded-xl overflow-hidden bg-black min-h-[260px]"></div>
                <p id="qrScannerStatus" class="text-center text-[11px] text-[#64748b] mt-3 font-semibold">Pointez la caméra vers le QR Code du patient...</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Prescriptions Bar Chart
        const monthlyData = @json($chartData);
        new Chart(document.getElementById('doctorMonthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.label),
                datasets: [{
                    label: 'Prescriptions',
                    data: monthlyData.map(d => d.count),
                    backgroundColor: 'rgba(0, 102, 255, 0.75)',
                    hoverBackgroundColor: 'rgba(0, 102, 255, 0.9)',
                    borderColor: '#0066ff',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Inter', size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    </script>
    <script>
        // Modal logic for previous exam details
        const detailsModal = document.getElementById('requestDetailsModal');
        const modalPatientName = document.getElementById('modalPatientName');
        const modalDate = document.getElementById('modalDate');
        const modalNotes = document.getElementById('modalNotes');
        const modalExamsList = document.getElementById('modalExamsList');

        document.querySelectorAll('.viewRequestBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const dataset = btn.dataset;
                modalPatientName.textContent = dataset.patient;
                modalDate.textContent = dataset.date;
                modalNotes.textContent = dataset.notes;

                const exams = JSON.parse(dataset.exams);
                modalExamsList.innerHTML = exams.map(exName => `
                    <div class="p-2.5 bg-[#F8FAFC] border border-[#e2e8f0] rounded-lg font-semibold text-[#1e293b] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#0066FF]"></span>
                        ${exName}
                    </div>
                `).join('');

                if (dataset.status === 'completed') {
                    document.getElementById('modalLabResultsSection').classList.remove('hidden');
                    document.getElementById('modalDoctorInterpretationSection').classList.remove('hidden');
                    document.getElementById('modalDoctorPrintBtn').classList.remove('hidden');
                    document.getElementById('modalDoctorPrintBtn').onclick = () => {
                        window.location.href = `/doctor/exam-requests/${dataset.id}/print?auto=1`;
                    };

                    const itemsResults = JSON.parse(dataset.itemsResults);
                    const resultsContainer = document.getElementById('modalLabResultsContainer');

                    resultsContainer.innerHTML = itemsResults.map(item => {
                        if (!item.result) {
                            return `
                                <div class="bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl p-3.5">
                                    <p class="font-bold text-[#1e293b]">${item.exam_name}</p>
                                    <p class="text-[#64748b] italic mt-1">Aucun résultat saisi pour cet examen.</p>
                                </div>
                            `;
                        }

                        const detailsRows = item.result.details.map(detail => {
                            let badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            if (detail.status === 'high') badgeClass = 'bg-red-50 text-red-700 border-red-200';
                            if (detail.status === 'low') badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            if (detail.status === 'critical') badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';

                            return `
                                <tr class="border-b border-[#e2e8f0]/40">
                                    <td class="py-2 pr-2 font-bold text-[#475569]">${detail.parameter}${detail.unit ? ` <span class="text-[9px] font-semibold text-[#7C3AED] bg-[#7C3AED]/10 px-1 py-0.5 rounded border border-[#7C3AED]/20">${detail.unit}</span>` : ''}</td>
                                    <td class="py-2 px-2 text-[#1e293b] font-extrabold">${detail.value}</td>
                                    <td class="py-2 px-2 text-[10px] text-[#64748b] font-medium">${detail.reference_range || '-'}</td>
                                    <td class="py-2 pl-2 text-right">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold border ${badgeClass} uppercase">${detail.status}</span>
                                    </td>
                                </tr>
                            `;
                        }).join('');

                        return `
                            <div class="bg-[#F8FAFC] border border-[#e2e8f0] rounded-xl p-3.5">
                                <p class="font-bold text-[#1e293b] text-xs border-b border-[#e2e8f0]/60 pb-2 mb-2">${item.exam_name}</p>
                                <table class="w-full text-left border-collapse mb-2.5">
                                    <thead>
                                        <tr class="text-[9px] text-[#64748b] uppercase tracking-wider font-bold">
                                            <th class="pb-1.5 pr-2">Paramètre</th>
                                            <th class="pb-1.5 px-2">Valeur</th>
                                            <th class="pb-1.5 px-2">Référence</th>
                                            <th class="pb-1.5 pl-2 text-right">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${detailsRows}
                                    </tbody>
                                </table>
                                ${item.result.interpretation ? `
                                    <div class="bg-purple-50/50 border border-[#7C3AED]/10 rounded-lg p-2.5 mt-2">
                                        <p class="text-[10px] font-bold text-[#7C3AED] uppercase tracking-wider">Interprétation Labo :</p>
                                        <p class="text-[11px] text-[#475569] mt-0.5 leading-relaxed">${item.result.interpretation}</p>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    }).join('');

                    if (dataset.approvedByDoctor === '1') {
                        document.getElementById('doctorInterpretationRead').classList.remove('hidden');
                        document.getElementById('doctorInterpretationRead').textContent = dataset.doctorInterpretation || 'Aucune interprétation saisie.';
                        document.getElementById('doctorInterpretationForm').classList.add('hidden');
                    } else {
                        document.getElementById('doctorInterpretationRead').classList.add('hidden');
                        document.getElementById('doctorInterpretationForm').classList.remove('hidden');

                        const form = document.getElementById('doctorInterpretationForm');
                        form.action = `/doctor/exam-requests/${dataset.id}/submit-interpretation`;
                        document.getElementById('modalDoctorInterpretationInput').value = '';
                    }
                } else {
                    document.getElementById('modalLabResultsSection').classList.add('hidden');
                    document.getElementById('modalDoctorInterpretationSection').classList.add('hidden');
                    document.getElementById('modalDoctorPrintBtn').classList.add('hidden');
                }

                detailsModal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.closeDetailsModalBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                detailsModal.classList.add('hidden');
            });
        });

        // Search modal logic
        const headerPatientSearchForm = document.getElementById('headerPatientSearchForm');
        const searchResultModal = document.getElementById('searchResultModal');
        const searchAvatar = document.getElementById('searchAvatar');
        const searchName = document.getElementById('searchName');
        const searchCodeBadge = document.getElementById('searchCodeBadge');
        const searchEmail = document.getElementById('searchEmail');
        const searchPhone = document.getElementById('searchPhone');

        const requestAccessBtn = document.getElementById('requestAccessBtn');
        const proceedToExamsBtn = document.getElementById('proceedToExamsBtn');
        const accessGrantedMessage = document.getElementById('accessGrantedMessage');
        const accessPendingMessage = document.getElementById('accessPendingMessage');

        let currentPatientId = null;

        headerPatientSearchForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const code = document.getElementById('header_patient_code').value.trim();
            if (!code) return;

            const submitBtn = headerPatientSearchForm.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

            try {
                const response = await fetch('{{ route('doctor.search-patient') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ patient_code: code }),
                });

                const data = await response.json();

                if (data.success) {
                    currentPatientId = data.patient.id;

                    const firstName = data.patient.user.first_name;
                    const lastName = data.patient.user.last_name;

                    searchAvatar.textContent = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
                    searchName.textContent = `${firstName} ${lastName}`;
                    searchCodeBadge.textContent = `Code: ${data.patient.patient_code}`;
                    searchEmail.textContent = `✉ ${data.patient.user.email}`;
                    searchPhone.textContent = `☎ ${data.patient.user.phone}`;

                    // Reset buttons
                    requestAccessBtn.classList.remove('hidden');
                    requestAccessBtn.disabled = false;
                    requestAccessBtn.innerHTML = "Demander l'Accès au Patient";
                    proceedToExamsBtn.classList.add('hidden');
                    accessGrantedMessage.classList.add('hidden');
                    accessPendingMessage.classList.add('hidden');

                    if (data.access_status === 'granted') {
                        requestAccessBtn.classList.add('hidden');
                        accessGrantedMessage.classList.remove('hidden');
                        proceedToExamsBtn.classList.remove('hidden');
                    } else if (data.access_status === 'pending') {
                        requestAccessBtn.classList.add('hidden');
                        accessPendingMessage.classList.remove('hidden');
                    } else if (data.access_status === 'blocked') {
                        requestAccessBtn.classList.add('hidden');
                        proceedToExamsBtn.classList.add('hidden');
                        accessGrantedMessage.classList.add('hidden');
                        accessPendingMessage.classList.add('hidden');
                        showSuccessToast('Vous êtes bloqué par ce patient.', 'error');
                    }

                    searchResultModal.classList.remove('hidden');
                } else {
                    showSuccessToast(data.message || 'Patient non trouvé', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showSuccessToast('Erreur lors de la recherche du patient.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        });

        // Close search result modal
        document.querySelectorAll('.closeSearchModalBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                searchResultModal.classList.add('hidden');
            });
        });

        // Request Access from dashboard modal
        requestAccessBtn.addEventListener('click', async () => {
            requestAccessBtn.disabled = true;
            requestAccessBtn.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';

            try {
                const response = await fetch('{{ route('doctor.request-access') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ patient_id: currentPatientId }),
                });

                const data = await response.json();

                if (data.success) {
                    if (data.access_granted) {
                        requestAccessBtn.classList.add('hidden');
                        accessGrantedMessage.classList.remove('hidden');
                        proceedToExamsBtn.classList.remove('hidden');
                        showSuccessToast('Accès autorisé !', 'success');
                    } else {
                        requestAccessBtn.classList.add('hidden');
                        accessPendingMessage.classList.remove('hidden');
                        showSuccessToast('Demande envoyée avec succès', 'success');
                    }
                } else {
                    showSuccessToast(data.message || 'Erreur lors de la demande.', 'error');
                    requestAccessBtn.disabled = false;
                    requestAccessBtn.innerHTML = "Demander l'Accès au Patient";
                }
            } catch (error) {
                console.error('Error:', error);
                showSuccessToast('Une erreur est survenue.', 'error');
                requestAccessBtn.disabled = false;
                requestAccessBtn.innerHTML = "Demander l'Accès au Patient";
            }
        });

        // Proceed to exams click inside dashboard search modal
        proceedToExamsBtn.addEventListener('click', () => {
            if (currentPatientId) {
                window.location.href = `/doctor/exams-selection/${currentPatientId}`;
            }
        });

        function showSuccessToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed top-20 right-4 ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'} text-white px-5 py-3 rounded-xl shadow-xl z-[100] flex items-center gap-2 text-xs font-bold`;
            toast.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        // ── Notifications ──
        const doctorNotifRoutes = {
            list: '{{ route("doctor.get-notifications") }}',
            unreadCount: '{{ route("doctor.unread-count") }}',
            markRead: '{{ route("doctor.mark-as-read", "__ID__") }}',
            markAllRead: '{{ route("doctor.mark-all-read") }}'
        };

        function doctorToggleNotifPanel() {
            const panel = document.getElementById('doctorNotifPanel');
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) {
                doctorLoadNotifications();
            }
        }

        async function doctorLoadNotifications() {
            try {
                const res = await fetch(doctorNotifRoutes.list);
                const data = await res.json();
                const list = document.getElementById('doctorNotifList');
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="px-4 py-6 text-center text-xs text-[#94a3b8]">Aucune notification</div>';
                    return;
                }
                list.innerHTML = data.notifications.map(n => `
                    <div class="px-4 py-3 hover:bg-[#f8fafc] transition cursor-pointer ${n.is_read ? '' : 'bg-[#eff6ff]/50'}" onclick="doctorMarkRead(${n.id}, this)">
                        <div class="flex items-start gap-2">
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 ${n.is_read ? 'bg-[#cbd5e1]' : 'bg-[#0066FF]'}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-[#1e293b]">${n.title}</div>
                                <div class="text-[11px] text-[#64748b] mt-0.5 line-clamp-2">${n.message}</div>
                                <div class="text-[10px] text-[#94a3b8] mt-1">${n.created_at}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } catch (e) {
                console.error('Notification load error:', e);
            }
        }

        async function doctorUpdateUnreadBadge() {
            try {
                const res = await fetch(doctorNotifRoutes.unreadCount);
                const data = await res.json();
                const badge = document.getElementById('doctorNotifBadge');
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (e) {}
        }

        async function doctorMarkRead(id, el) {
            try {
                await fetch(doctorNotifRoutes.markRead.replace('__ID__', id), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                doctorUpdateUnreadBadge();
                doctorLoadNotifications();
            } catch (e) {}
        }

        async function doctorMarkAllRead() {
            try {
                await fetch(doctorNotifRoutes.markAllRead, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                doctorUpdateUnreadBadge();
                doctorLoadNotifications();
            } catch (e) {}
        }

        // Close panel on outside click
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('doctorNotifWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('doctorNotifPanel').classList.add('hidden');
            }
        });

        // ── QR Code Camera Scanner ──
        let qrCodeScanner = null;

        document.getElementById('openQrScannerBtn').addEventListener('click', () => {
            document.getElementById('qrScannerOverlay').classList.remove('hidden');
            startQrScanner();
        });

        document.getElementById('closeQrScannerBtn').addEventListener('click', stopQrScanner);

        document.getElementById('qrScannerOverlay').addEventListener('click', (e) => {
            if (e.target === e.currentTarget) stopQrScanner();
        });

        function startQrScanner() {
            const statusEl = document.getElementById('qrScannerStatus');
            statusEl.textContent = 'Initialisation de la caméra...';

            qrCodeScanner = new Html5Qrcode('qrReaderRegion');
            qrCodeScanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    stopQrScanner();
                    let code = decodedText.trim();
                    if (code.includes('/')) {
                        const parts = code.split('/');
                        code = parts[parts.length - 1];
                    }
                    document.getElementById('header_patient_code').value = code;
                    headerPatientSearchForm.dispatchEvent(new Event('submit'));
                },
                () => {}
            ).catch(() => {
                statusEl.textContent = 'Caméra indisponible. Veuillez scanner manuellement.';
            });
        }

        function stopQrScanner() {
            if (qrCodeScanner) {
                qrCodeScanner.stop().then(() => {
                    qrCodeScanner.clear();
                    qrCodeScanner = null;
                }).catch(() => { qrCodeScanner = null; });
            }
            document.getElementById('qrScannerOverlay').classList.add('hidden');
        }

        // Initial load + auto-refresh
        doctorUpdateUnreadBadge();
        setInterval(doctorUpdateUnreadBadge, 15000);

        // Auto-search from QR code scan
        const urlParams = new URLSearchParams(window.location.search);
        const scanCode = urlParams.get('scan');
        if (scanCode) {
            document.getElementById('header_patient_code').value = scanCode;
            headerPatientSearchForm.dispatchEvent(new Event('submit'));
            window.history.replaceState({}, '', window.location.pathname);
        }
    </script>
    @endsection
</x-layouts.doctor>
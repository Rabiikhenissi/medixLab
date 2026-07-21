@extends('layouts.center')

@section('title', 'Demandes d\'analyses - Medix eSanté')

@section('content')

<div class="space-y-6 select-none">

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#1e293b]">Demandes d'analyses</h1>
            <p class="text-sm text-[#64748b] mt-2">Consultez et prenez en charge les demandes d'analyses assignées à votre laboratoire.</p>
        </div>
        <div id="machineStatus" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border bg-gray-50 border-gray-200 text-gray-400">
            <span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span>
            Vérification machine...
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <!-- Search & Filter -->
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Recherche</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom patient, médecin, n° demande..."
                class="border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm text-[#1e293b] placeholder:text-[#94a3b8] focus:border-[#0066ff] focus:ring-1 focus:ring-[#0066ff] outline-none w-64">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Statut</label>
            <select name="status" class="border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm text-[#1e293b] focus:border-[#0066ff] focus:ring-1 focus:ring-[#0066ff] outline-none">
                <option value="">Tous</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="assigned" {{ ($status ?? '') === 'assigned' ? 'selected' : '' }}>Assignée</option>
                <option value="collected" {{ ($status ?? '') === 'collected' ? 'selected' : '' }}>Collectée</option>
                <option value="processing" {{ ($status ?? '') === 'processing' ? 'selected' : '' }}>En traitement</option>
                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Terminée</option>
                <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>
        <button type="submit" class="bg-[#0066ff] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#0052cc] transition">Filtrer</button>
        @if(($search ?? '') || ($status ?? ''))
            <a href="{{ route('center.exam-requests') }}" class="text-sm text-[#64748b] hover:text-[#0066ff] font-medium px-3 py-2.5">Réinitialiser</a>
        @endif
    </form>

    <!-- Table -->
    <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F8FAFC] border-b border-[#e2e8f0]">
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Patient</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Médecin</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Examens</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">Statut</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Date</th>
                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($requests as $request)
                <tr class="hover:bg-[#F8FAFC]/70 transition">
                    <!-- Patient -->
                    <td class="p-4">
                        <div class="font-bold text-[#1e293b]">{{ $request->patient->user->first_name }} {{ $request->patient->user->last_name }}</div>
                        <div class="text-xs text-[#64748b] mt-1">Demande #{{ $request->id }}</div>
                    </td>

                    <!-- Doctor -->
                    <td class="p-4">
                        <div class="text-sm font-semibold text-[#475569]">Dr. {{ $request->doctor->user->first_name }} {{ $request->doctor->user->last_name }}</div>
                    </td>

                    <!-- Exams -->
                    <td class="p-4">
                        <div class="space-y-2">
                        @foreach($request->items as $item)
                            <div class="flex items-center gap-2 text-xs font-semibold text-[#475569]">
                                @if($item->resultLabo)
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#7C3AED]"></span>
                                @endif
                                {{ $item->exam->name }}
                            </div>
                        @endforeach
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="p-4 text-center">
                        @if($request->status === 'assigned')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-50 text-orange-600 border border-orange-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>Assignée
                        </span>
                        @elseif($request->status === 'processing')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>En traitement
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Terminé
                        </span>
                        @endif
                    </td>

                    <!-- Date -->
                    <td class="p-4 text-xs font-semibold text-[#64748b]">{{ $request->created_at->format('d/m/Y H:i') }}</td>

                    <!-- Actions -->
                    <td class="p-4">
                        <div class="flex flex-col gap-1.5 items-end">

                        @if($request->status === 'assigned')
                            <form method="POST" action="{{ route('center.exam-requests.claim',$request) }}">
                                @csrf
                                <button class="px-4 py-2 bg-[#7C3AED] hover:bg-[#6D28D9] text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-md shadow-purple-200">
                                    Prendre en charge
                                </button>
                            </form>
                        @else
                            @foreach($request->items as $item)
                                @php
                                    $hasResult = $item->resultLabo;
                                    $dropdownId = 'item-dd-' . $item->id;
                                @endphp
                                <div class="relative flex items-center gap-2 justify-end">
                                    <span class="text-[10px] font-semibold text-[#64748b] truncate max-w-[120px]" title="{{ $item->exam->name }}">
                                        {{ $item->exam->name }}
                                    </span>

                                    @if($hasResult)
                                        <a href="{{ route('center.results.edit', $item->resultLabo) }}"
                                           class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap">
                                            ✓ Résultat
                                        </a>
                                    @else
                                        <button type="button" onclick="toggleItemDropdown('{{ $dropdownId }}')"
                                            class="item-dropdown-trigger px-2.5 py-1 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] border border-[#e2e8f0] hover:border-[#0066ff]/30 rounded-lg text-[10px] font-bold uppercase tracking-wider transition whitespace-nowrap flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z"/></svg>
                                            Saisir
                                        </button>

                                        <div id="{{ $dropdownId }}" class="hidden absolute right-0 top-full mt-1 w-56 bg-white border border-[#e2e8f0] rounded-xl shadow-2xl z-50 overflow-hidden">
                                            <a href="{{ route('center.results.create', $item) }}"
                                               class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-[#1e293b] hover:bg-[#f0fdfa] transition">
                                                <span class="w-8 h-8 rounded-lg bg-[#7C3AED]/10 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </span>
                                                <div>
                                                    <div class="font-bold">Saisir manuellement</div>
                                                    <div class="text-[10px] text-[#94a3b8] font-normal">Entrer les résultats vous-même</div>
                                                </div>
                                            </a>
                                            <div class="border-t border-[#f1f5f9]"></div>
                                            <form method="POST" action="{{ route('center.machine.send', $item) }}">
                                                @csrf
                                                <button type="submit" onclick="machineSendClick(this)"
                                                    class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-[#1e293b] hover:bg-blue-50/50 transition machine-send-btn text-left">
                                                    <span class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
                                                    </span>
                                                    <div>
                                                        <div class="font-bold machine-btn-text">Envoyer à la machine</div>
                                                        <div class="text-[10px] text-[#94a3b8] font-normal machine-btn-sub">Analyse automatique HL7</div>
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400 font-semibold">Aucune demande d'analyse assignée.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function checkMachineStatus() {
        fetch('{{ route("center.machine.status") }}')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('machineStatus');
                if (data.online) {
                    el.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border bg-emerald-50 border-emerald-200 text-emerald-600';
                    el.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span>' + (data.info?.machine || 'Machine connectée');
                } else {
                    el.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border bg-red-50 border-red-200 text-red-500';
                    el.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500"></span>Machine hors ligne';
                }
            })
            .catch(() => {
                const el = document.getElementById('machineStatus');
                el.className = 'flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border bg-red-50 border-red-200 text-red-500';
                el.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500"></span>Machine hors ligne';
            });
    }
    checkMachineStatus();
    setInterval(checkMachineStatus, 15000);

    function toggleItemDropdown(id) {
        const dd = document.getElementById(id);
        const isOpen = !dd.classList.contains('hidden');
        document.querySelectorAll('[id^="item-dd-"]').forEach(el => el.classList.add('hidden'));
        if (!isOpen) dd.classList.remove('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.item-dropdown-trigger') && !e.target.closest('[id^="item-dd-"]')) {
            document.querySelectorAll('[id^="item-dd-"]').forEach(el => el.classList.add('hidden'));
        }
    });

    function machineSendClick(btn) {
        btn.disabled = true;
        const textEl = btn.querySelector('.machine-btn-text');
        const subEl = btn.querySelector('.machine-btn-sub');
        if (textEl) textEl.textContent = 'Analyse en cours...';
        if (subEl) subEl.textContent = 'Patientez...';
        btn.classList.add('opacity-60', 'cursor-wait');
    }
</script>
@endsection

<x-layouts.auth>
    <x-slot:title>Mes Patients - Medix eSanté</x-slot:title>

    <div class="w-full max-w-[1200px] mx-auto py-8 px-4">
        <div class="glass-card rounded-[20px] p-8 md:p-10">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0066FF] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-[#1e293b]">Mes Patients</h1>
                        <span class="text-xs text-[#64748b]">{{ $accesses->count() }} patient(s) avec accès actif</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('doctor.dashboard') }}"
                       class="text-sm font-semibold text-[#64748b] hover:text-[#0066FF] transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Tableau de bord
                    </a>
                </div>
            </div>

            {{-- Patient Grid --}}
            @if($accesses->isEmpty())
                <div class="text-center py-20">
                    <div class="w-16 h-16 bg-[#f1f5f9] rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-[#94a3b8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#1e293b] mb-2">Aucun patient actif</h3>
                    <p class="text-sm text-[#64748b]">Vous n'avez pas encore de patients avec un accès accordé.</p>
                    <a href="{{ route('doctor.patient-search') }}"
                       class="mt-4 inline-flex items-center px-4 py-2 bg-[#0066FF] text-white text-sm font-semibold rounded-xl hover:bg-[#0052cc] transition">
                        Rechercher un patient
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($accesses as $access)
                        @php $patient = $access->patient; $user = $patient->user; @endphp
                        <div class="bg-white rounded-2xl border border-[#e2e8f0] shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                            {{-- Patient Header --}}
                            <div class="bg-gradient-to-br from-[#0066FF] to-[#3b82f6] p-5 text-white relative">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($user->first_name,0,1)) }}{{ strtoupper(substr($user->last_name,0,1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-base">{{ $user->first_name }} {{ $user->last_name }}</p>
                                        <p class="text-xs text-white/80">{{ $patient->patient_code }}</p>
                                    </div>
                                </div>
                                @if($access->expires_at)
                                    <div class="mt-3 text-[10px] text-white/70">
                                        Accès expire le {{ $access->expires_at->format('d/m/Y') }}
                                        @if($access->isExpired())
                                            <span class="ml-1 px-1.5 py-0.5 bg-red-500 rounded text-white">EXPIRÉ</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Patient Info --}}
                            <div class="p-5 space-y-3">
                                <div class="flex items-center gap-2 text-sm text-[#64748b]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $user->email }}
                                </div>
                                @if($patient->blood_group)
                                    <div class="flex items-center gap-2 text-sm text-[#64748b]">
                                        <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        </svg>
                                        Groupe sanguin : <strong class="text-[#1e293b]">{{ $patient->blood_group }}</strong>
                                    </div>
                                @endif

                                {{-- Recent Exams --}}
                                @if($patient->examRequests->count() > 0)
                                    <div class="pt-3 border-t border-[#f1f5f9]">
                                        <p class="text-xs font-semibold text-[#94a3b8] uppercase tracking-wider mb-2">Dernières demandes</p>
                                        <div class="space-y-1.5">
                                            @foreach($patient->examRequests->take(3) as $req)
                                                @php
                                                    $statusColors = [
                                                        'pending'    => 'bg-amber-100 text-amber-700',
                                                        'assigned'   => 'bg-blue-100 text-blue-700',
                                                        'processing' => 'bg-indigo-100 text-indigo-700',
                                                        'completed'  => 'bg-emerald-100 text-emerald-700',
                                                        'cancelled'  => 'bg-red-100 text-red-700',
                                                    ];
                                                    $statusLabels = [
                                                        'pending'    => 'En attente',
                                                        'assigned'   => 'Assigné',
                                                        'processing' => 'En cours',
                                                        'completed'  => 'Terminé',
                                                        'cancelled'  => 'Annulé',
                                                    ];
                                                @endphp
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-[#475569]">{{ $req->created_at->format('d/m/Y') }}</span>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="px-2 py-0.5 rounded-full font-semibold {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                            {{ $statusLabels[$req->status] ?? $req->status }}
                                                        </span>
                                                        @if($req->status === 'completed')
                                                            <a href="{{ route('doctor.print-exam-request', $req->id) }}?auto=1" class="text-[#0066FF] hover:text-[#0052cc] inline-flex items-center p-1" title="Imprimer le rapport PDF">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                                </svg>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="px-5 pb-5 flex gap-2">
                                <a href="{{ route('doctor.select-exams', $patient->id) }}"
                                   class="flex-1 text-center text-xs font-semibold py-2 px-3 bg-[#0066FF] text-white rounded-xl hover:bg-[#0052cc] transition">
                                    Prescrire
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-layouts.auth>

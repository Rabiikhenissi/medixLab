<x-layouts.patient>
    <x-slot:title>Médecin trouvé — Medix eSanté</x-slot:title>

    @section('content')
    <div class="w-full max-w-[700px] mx-auto">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center space-x-4 border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0D9488] flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-[#1e293b]">Médecin trouvé</h1>
                    <p class="text-sm text-[#64748b] mt-0.5">
                        QR code scanné avec succès — vérifiez les informations puis confirmez.
                    </p>
                </div>
            </div>

            @if ($existing && $existing->access_status === 'blocked')
                <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-700">Ce médecin est bloqué</p>
                        <p class="text-xs text-red-600 mt-1">Vous ne pouvez pas vous lier à ce médecin. Débloquez-le depuis votre espace si vous souhaitez restaurer la relation.</p>
                    </div>
                </div>
            @elseif ($existing && $existing->access_status === 'granted')
                <div class="mb-6 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-green-700">Vous êtes déjà lié(e) à ce médecin</p>
                        <p class="text-xs text-green-600 mt-1">Votre dossier médical est déjà partagé avec ce médecin.</p>
                    </div>
                </div>
            @endif

            {{-- Doctor card --}}
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-xs">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center text-white font-bold text-xl shadow-lg flex-shrink-0">
                        {{ strtoupper(substr($doctor->user->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->user->last_name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-[#1e293b]">
                            Dr. {{ $doctor->user->first_name }} {{ $doctor->user->last_name }}
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0066FF] bg-[#0066FF]/10 border border-[#0066FF]/20 uppercase mt-1">
                            {{ $doctor->speciality }}
                        </span>
                        <p class="text-xs text-[#64748b] mt-2 font-mono font-semibold">Code : {{ $doctor->doctor_code }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6">
                    <div class="flex items-center gap-2 text-xs text-[#64748b]">
                        <svg class="w-4 h-4 text-[#0D9488] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        <span class="truncate">{{ $doctor->user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-[#64748b]">
                        <svg class="w-4 h-4 text-[#0D9488] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                        </svg>
                        <span>{{ $doctor->user->phone ?? 'Non renseigné' }}</span>
                    </div>
                </div>
            </div>

            {{-- Action area --}}
            <div class="mt-8">
                @if ($existing && $existing->access_status === 'blocked')
                    <a href="{{ route('patient.dashboard') }}"
                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-white bg-[#0D9488] hover:bg-[#0a7068] transition uppercase tracking-wider">
                        Retour au tableau de bord
                    </a>
                @elseif ($existing && $existing->access_status === 'granted')
                    <a href="{{ route('patient.dashboard') }}"
                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-white bg-[#0D9488] hover:bg-[#0a7068] transition uppercase tracking-wider">
                        Retour au tableau de bord
                    </a>
                @else
                    <div class="mb-4 flex items-start gap-3 p-4 bg-[#0D9488]/5 border border-[#0D9488]/20 rounded-xl">
                        <svg class="w-5 h-5 text-[#0D9488] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <p class="text-xs text-[#475569] leading-relaxed">
                            En confirmant, ce médecin aura accès à votre dossier médical pour vous prescrire et suivre vos analyses.
                            <span class="font-bold text-[#0D9488]">La liaison est immédiate — aucune approbation requise.</span>
                        </p>
                    </div>
                    <form action="{{ route('patient.scan-doctor-link', $doctor->doctor_code) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl text-xs font-bold text-white bg-[#0D9488] hover:bg-[#0a7068] transition uppercase tracking-wider shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                            Devenir patient de ce médecin
                        </button>
                    </form>
                    <a href="{{ route('patient.dashboard') }}"
                       class="mt-3 w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#64748b] bg-[#f1f5f9] hover:bg-[#e2e8f0] transition uppercase tracking-wider">
                        Annuler
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endsection
</x-layouts.patient>

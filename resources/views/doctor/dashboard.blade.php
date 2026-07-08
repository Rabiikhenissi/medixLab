<x-layouts.auth>
    <x-slot:title>Tableau de bord Médecin - Medix eSanté</x-slot:title>

    <div class="w-full max-w-[800px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0066FF] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20V8l8 7 8-7v12" />
                            <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0066FF" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#1e293b]">Espace Médecin</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0066FF] bg-[#0066FF]/10 border border-[#0066FF]/20 uppercase mt-1">
                            PROFESSIONNEL DE SANTÉ
                        </span>
                    </div>
                </div>

                <div>
                    <form action="{{ route('doctor.logout') }}" method="POST">
                        @csrf
                        <x-button type="submit" color="slate" :fullWidth="false" class="!py-1.5 !px-4 !text-xs">
                            SE DÉCONNECTER
                        </x-button>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Greeting -->
                <div>
                    <h1 class="text-2xl font-bold text-[#1e293b]">
                        Bonjour, Dr. <span class="text-[#0066FF]">{{ $user->first_name }} {{ $user->last_name }}</span> !
                    </h1>
                    <p class="text-sm text-[#64748b] mt-1 font-medium leading-relaxed">
                        Bienvenue sur votre tableau de bord sécurisé. Vous pouvez gérer vos informations et vos prescriptions.
                    </p>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <!-- Profile Info -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#0066FF] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Informations Professionnelles
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Numéro CNOM (Code) :</span>
                                <span class="font-bold text-[#1e293b]">{{ $user->doctor->doctor_code }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Spécialité :</span>
                                <span class="font-semibold text-[#1e293b] capitalize">{{ $user->doctor->speciality }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Adresse Email :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-[#64748b] font-medium">Téléphone :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions & Stats Summary -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#0066FF] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Statistiques & Activité
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#0066FF]/10 flex items-center justify-center text-[#0066FF] font-bold text-sm">
                                        {{ $user->doctor->examGroups()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Groupes d'examens prescrits</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#0066FF]/10 flex items-center justify-center text-[#0066FF] font-bold text-sm">
                                        {{ $user->doctor->examRequests()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Demandes d'examens envoyées</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section link details -->
                <div class="bg-blue-50/40 border border-[#0066FF]/10 rounded-2xl p-6 select-none">
                    <h4 class="text-xs font-bold text-[#0066FF] uppercase tracking-wider mb-2">Note d'intégration</h4>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Ce tableau de bord est entièrement dynamique et connecté aux modèles Eloquent de la base de données (<span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\Doctor</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\User</span>). Vos informations d'inscription ont été correctement insérées.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>

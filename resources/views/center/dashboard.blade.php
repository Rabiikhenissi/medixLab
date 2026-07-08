<x-layouts.auth>
    <x-slot:title>Tableau de bord Centre Médical - Medix eSanté</x-slot:title>

    <div class="w-full max-w-[800px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-[#7C3AED] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#7C3AED]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20V8l8 7 8-7v12" />
                            <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#7C3AED" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#1e293b]">Espace Etablissement</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#7C3AED] bg-[#7C3AED]/10 border border-[#7C3AED]/20 uppercase mt-1">
                            ADMINISTRATION
                        </span>
                    </div>
                </div>

                <div>
                    <form action="{{ route('center.logout') }}" method="POST">
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
                        Bienvenue, <span class="text-[#7C3AED]">{{ $user->staff->laboratory->name }}</span> !
                    </h1>
                    <p class="text-sm text-[#64748b] mt-1 font-medium leading-relaxed">
                        Responsable : <span class="font-semibold text-[#1e293b]">{{ $user->first_name }} {{ $user->last_name }}</span>.
                        Gérez vos équipements, vos consommables et les demandes d'examens de votre centre.
                    </p>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <!-- Profile Info -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#7C3AED] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
                            </svg>
                            Détails de l'Etablissement
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Nom du Centre :</span>
                                <span class="font-bold text-[#1e293b]">{{ $user->staff->laboratory->name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Code Unique Responsable :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->staff->staff_code }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Adresse Email :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Téléphone :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->phone }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-[#64748b] font-medium">Ville :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->staff->laboratory->city ?? 'Non renseignée' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lab Inventory & Activity -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#7C3AED] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Inventaire & Activité Labo
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#7C3AED]/10 flex items-center justify-center text-[#7C3AED] font-bold text-sm">
                                        {{ $user->staff->laboratory->equipment()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Equipements sous gestion</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#7C3AED]/10 flex items-center justify-center text-[#7C3AED] font-bold text-sm">
                                        {{ $user->staff->laboratory->consumables()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Consommables enregistrés</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section link details -->
                <div class="bg-purple-50/40 border border-[#7C3AED]/10 rounded-2xl p-6 select-none">
                    <h4 class="text-xs font-bold text-[#7C3AED] uppercase tracking-wider mb-2">Note d'intégration</h4>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Ce tableau de bord est entièrement dynamique et connecté aux modèles Eloquent de la base de données (<span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\Labo</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\Staff</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\User</span>). Vos informations d'inscription ont été correctement insérées.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>

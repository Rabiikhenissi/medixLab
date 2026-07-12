@extends('layouts.center')

@section('title', 'Tableau de bord Centre Médical')

@section('content')
    <div class="space-y-6 select-none">
        
        <!-- Greeting -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-[#1e293b]">
                Bienvenue, <span class="text-[#7C3AED]">{{ auth()->user()->staff->laboratory->name }}</span> !
            </h2>
            <p class="text-sm text-[#64748b] mt-1 font-medium leading-relaxed">
                Gérez vos horaires de travail, surveillez le stock de vos consommables, et suivez la maintenance de vos équipements médicaux.
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Equipements Card -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs relative overflow-hidden group hover:border-[#7C3AED]/30 transition">
                <div class="text-3xl font-black text-[#1e293b] mb-1">{{ $stats['equipment_count'] }}</div>
                <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Équipements</p>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-[#7C3AED] group-hover:scale-110 transition duration-300">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 8H5c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 10h-2v-2h2v2zm0-4h-2v-4h2v4zm1-8h-4V1h4v3z"/>
                    </svg>
                </div>
            </div>

            <!-- Consommables Card -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-5 shadow-xs relative overflow-hidden group hover:border-[#7C3AED]/30 transition">
                <div class="text-3xl font-black text-[#1e293b] mb-1">{{ $stats['consumables_count'] }}</div>
                <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Consommables</p>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-[#7C3AED] group-hover:scale-110 transition duration-300">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
            </div>

            <!-- Alerte Stock Card -->
            <div class="border rounded-2xl p-5 shadow-xs relative overflow-hidden group transition {{ $stats['low_stock_count'] > 0 ? 'bg-red-50/50 border-red-200' : 'bg-white border-[#e2e8f0] hover:border-[#7C3AED]/30' }}">
                <div class="text-3xl font-black mb-1 {{ $stats['low_stock_count'] > 0 ? 'text-red-600' : 'text-[#1e293b]' }}">{{ $stats['low_stock_count'] }}</div>
                <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Alerte Stock Bas</p>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-red-600 group-hover:scale-110 transition duration-300">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            </div>

            <!-- Maintenance en Cours Card -->
            <div class="border rounded-2xl p-5 shadow-xs relative overflow-hidden group transition {{ $stats['active_maintenance_count'] > 0 ? 'bg-amber-50/50 border-amber-200' : 'bg-white border-[#e2e8f0] hover:border-[#7C3AED]/30' }}">
                <div class="text-3xl font-black mb-1 {{ $stats['active_maintenance_count'] > 0 ? 'text-amber-600' : 'text-[#1e293b]' }}">{{ $stats['active_maintenance_count'] }}</div>
                <p class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Maintenances Actives</p>
                <div class="absolute -right-2 -bottom-2 opacity-5 text-amber-600 group-hover:scale-110 transition duration-300">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.3C.5 6.7.9 9.8 2.9 11.8c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Info Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <!-- Profile Info -->
            <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                <h3 class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 text-[#7C3AED] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
                    </svg>
                    Détails de l'Établissement
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                        <span class="text-[#64748b] font-medium">Nom du Centre :</span>
                        <span class="font-bold text-[#1e293b]">{{ auth()->user()->staff->laboratory->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                        <span class="text-[#64748b] font-medium">Code Unique Responsable :</span>
                        <span class="font-semibold text-[#1e293b]">{{ auth()->user()->staff->staff_code }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                        <span class="text-[#64748b] font-medium">Adresse Email :</span>
                        <span class="font-semibold text-[#1e293b]">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                        <span class="text-[#64748b] font-medium">Téléphone :</span>
                        <span class="font-semibold text-[#1e293b]">{{ auth()->user()->phone }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-[#64748b] font-medium">Ville :</span>
                        <span class="font-semibold text-[#1e293b]">{{ auth()->user()->staff->laboratory->city ?? 'Non renseignée' }}</span>
                    </div>
                </div>
            </div>

            <!-- Lab Inventory & Quick Links -->
            <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                        <svg class="w-4 h-4 text-[#7C3AED] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Actions Rapides
                    </h3>
                    <p class="text-xs text-[#64748b] leading-relaxed mb-4">
                        Accédez rapidement aux différentes sections pour configurer votre établissement médical.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <a href="{{ route('center.working-hours') }}" class="p-3 bg-white border border-[#e2e8f0] rounded-xl hover:border-[#7C3AED]/30 transition text-xs font-bold text-[#475569] hover:text-[#7C3AED] shadow-2xs">
                        Gérer Horaires
                    </a>
                    <a href="{{ route('center.consumables') }}" class="p-3 bg-white border border-[#e2e8f0] rounded-xl hover:border-[#7C3AED]/30 transition text-xs font-bold text-[#475569] hover:text-[#7C3AED] shadow-2xs">
                        Gérer Stock
                    </a>
                    <a href="{{ route('center.equipment') }}" class="p-3 bg-white border border-[#e2e8f0] rounded-xl hover:border-[#7C3AED]/30 transition text-xs font-bold text-[#475569] hover:text-[#7C3AED] shadow-2xs">
                        Gérer Équipements
                    </a>
                    <div class="p-3 bg-purple-50/50 border border-purple-100 rounded-xl text-[10px] font-bold text-[#7C3AED] flex items-center justify-center">
                        {{ auth()->user()->staff->laboratory->city ?? 'France' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-purple-50/40 border border-[#7C3AED]/10 rounded-2xl p-6">
            <h4 class="text-xs font-bold text-[#7C3AED] uppercase tracking-wider mb-2">Note d'intégration</h4>
            <p class="text-xs text-[#64748b] leading-relaxed">
                Ce tableau de bord est entièrement dynamique et connecté aux modèles Eloquent de la base de données (<span class="font-mono bg-white px-1 py-0.5 rounded border text-[#1e293b]">App\Models\Labo</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border text-[#1e293b]">App\Models\Staff</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border text-[#1e293b]">App\Models\User</span>). Vos informations d'inscription ont été correctement insérées.
            </p>
        </div>
    </div>
@endsection

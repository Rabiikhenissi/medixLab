<x-layouts.auth>
    <x-slot:title>{{ $legalTitle ?? 'Informations légales' }} - Medix eSanté</x-slot:title>

    <div class="w-full max-w-3xl mx-auto py-10 px-4 md:px-6">
        <div class="glass-card rounded-[20px] p-8 md:p-12">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-xs font-bold tracking-widest text-[#0066FF] uppercase">Medix eSanté</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-[#1e293b] tracking-tight mb-8">{{ $legalTitle }}</h1>

            <div class="text-sm text-[#475569] leading-relaxed space-y-5">
                {{ $slot }}
            </div>

            <div class="mt-10 pt-6 border-t border-[#e2e8f0]/80 flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-[#0066FF] hover:underline">
                    &larr; Retour à l'accueil
                </a>
                <span class="text-xs text-[#94a3b8]">Dernière mise à jour : {{ $updatedAt ?? now()->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</x-layouts.auth>

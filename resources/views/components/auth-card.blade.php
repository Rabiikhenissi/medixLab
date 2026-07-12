@props([
    'title' => '',
    'subtitle' => '',
    'badge' => null,
    'action' => '#',
    'backUrl' => null
])

<div class="w-full max-w-[458px] mx-auto py-8">
    <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="absolute top-6 left-6 text-[#64748b] hover:text-[#1e293b] transition-colors duration-200" title="Retour">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
        @endif
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6 select-none">
            <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0066FF] flex items-center justify-center shadow-sm mb-2 transition-transform duration-300 hover:scale-105">
                <svg class="w-6 h-6 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 20V8l8 7 8-7v12" />
                    <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0D9488" />
                </svg>
            </div>
            <span class="text-xs font-bold tracking-widest text-[#0066FF] uppercase">Medix eSanté</span>
        </div>

        <!-- Headings -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-semibold text-[#1e293b] tracking-tight">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-sm text-[#64748b] mt-1.5 font-medium leading-relaxed">{{ $subtitle }}</p>
            @endif

            <!-- Role Badge -->
            @if($badge)
                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-bold tracking-wider text-[#0066FF] bg-[#0066FF]/10 border border-[#0066FF]/20 uppercase">
                        {{ $badge }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Form Slots -->
        <form action="{{ $action }}" method="POST" class="space-y-4">
            @csrf
            {{ $slot }}
        </form>

        <!-- Footer -->
        @if(isset($footer))
            <div class="mt-6 pt-6 border-t border-[#e2e8f0]/80 text-center text-xs text-[#64748b] leading-relaxed">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

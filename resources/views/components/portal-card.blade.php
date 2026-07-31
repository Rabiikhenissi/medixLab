@props([
    'title',
    'description',
    'iconBg' => 'bg-emerald-50',
    'primaryColor' => 'blue',
    'loginUrl',
    'registerUrl',
    'loginText',
    'registerText' => "Créer un compte"
])

<div class="glass-card rounded-[24px] p-8 flex flex-col justify-between items-center text-center h-full max-w-[340px] w-full transition-all duration-300">
    <div class="flex flex-col items-center">
        <!-- Icon Wrapper -->
        <div class="w-16 h-16 rounded-full {{ $iconBg }} flex items-center justify-center mb-6 shadow-inner">
            {{ $slot }}
        </div>

        <!-- Title -->
        <h3 class="text-lg font-bold text-[#1e293b] mb-3 tracking-tight">{{ $title }}</h3>

        <!-- Description -->
        <p class="text-xs text-[#64748b] leading-relaxed font-medium mb-6">
            {{ $description }}
        </p>
    </div>

    <!-- Buttons -->
    <div class="w-full space-y-3">
        <!-- Login Button -->
        <a href="{{ $loginUrl }}" class="block">
            <x-button color="{{ $primaryColor }}" :fullWidth="true">
                {{ $loginText }}
            </x-button>
        </a>

        <!-- Register Link -->
        <a href="{{ $registerUrl }}" class="block text-xs font-semibold text-[#64748b] hover:text-[#1e293b] transition-colors py-1">
            {{ $registerText }}
        </a>
    </div>
</div>

<x-layouts.auth>
    <x-slot:title>{{ __('errors.404_title') }} — {{ config('app.name') }}</x-slot:title>
    <x-slot:metaDescription>{{ __('errors.404_title') }}</x-slot:metaDescription>
    <x-slot:extraHead><meta name="robots" content="noindex"></x-slot:extraHead>

    <div class="flex flex-col items-center justify-center text-center py-16">
        <p class="text-8xl font-extrabold text-[#0066FF]/20 select-none">404</p>
        <h1 class="text-2xl md:text-3xl font-bold text-[#1e293b] mb-2">{{ __('errors.404_title') }}</h1>
        <p class="text-sm text-[#64748b] mb-10 max-w-md leading-relaxed">{{ __('errors.404_message') }}</p>
        <a href="{{ route('home') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#0066FF] text-white text-sm font-bold shadow-lg shadow-blue-500/30 hover:bg-[#0052CC] transition-colors">
            {{ __('errors.back_home') }}
        </a>
    </div>
</x-layouts.auth>

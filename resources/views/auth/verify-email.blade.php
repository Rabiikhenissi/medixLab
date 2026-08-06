<x-layouts.auth>
    <x-slot:title>{{ __('verify.meta_title') }}</x-slot:title>

    <div class="w-full max-w-[458px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <!-- Logo -->
            <div class="flex flex-col items-center mb-6 select-none">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0066FF] flex items-center justify-center shadow-sm mb-2">
                    <svg class="w-6 h-6 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20V8l8 7 8-7v12" />
                        <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0D9488" />
                    </svg>
                </div>
                <span class="text-xs font-bold tracking-widest text-[#0066FF] uppercase">{{ __('app.brand') }}</span>
            </div>

            <!-- Mail icon -->
            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3" />
                        <path d="M2 7l9.3 6a1 1 0 0 0 1.4 0L22 7" />
                    </svg>
                </div>
            </div>

            <!-- Headings -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-semibold text-[#1e293b] tracking-tight">{{ __('verify.title') }}</h2>
                <p class="text-sm text-[#64748b] mt-1.5 font-medium leading-relaxed">
                    {{ __('verify.subtitle') }}
                </p>
                <div class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0f172a] bg-[#F1F5F9] border border-[#e2e8f0] rounded-lg px-3 py-1.5">
                    {{ auth()->user()->email }}
                </div>
                <p class="text-xs text-[#94a3b8] mt-3 leading-relaxed">
                    {{ __('verify.help') }}
                </p>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Resend -->
            <form action="{{ route('verification.send') }}" method="POST" class="space-y-4">
                @csrf
                <button type="submit" class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl py-2.5 px-5 text-sm font-semibold tracking-wide transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]/20 cursor-pointer">
                    {{ __('verify.submit') }}
                </button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-xs text-[#94a3b8]">{{ __('verify.spam') }}</p>
            </div>

            <!-- Footer -->
            <div class="mt-6 pt-6 border-t border-[#e2e8f0]/80 text-center text-xs text-[#64748b] leading-relaxed">
                <form action="{{ route(auth()->user()->staff ? 'center.logout' : (auth()->user()->doctor ? 'doctor.logout' : 'patient.logout')) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>

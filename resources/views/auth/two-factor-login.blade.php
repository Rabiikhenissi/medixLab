<x-layouts.auth>
    <x-slot:title>{{ __('twofactor.meta_title') }}</x-slot:title>

    <div class="w-full max-w-md mx-auto py-8">
        <div class="glass-card rounded-2xl p-8 md:p-10 relative overflow-hidden">
            <a href="{{ route('home') }}" class="absolute top-5 left-5 w-9 h-9 rounded-lg bg-white/60 hover:bg-white flex items-center justify-center text-[#64748b] hover:text-[#1e293b] transition-all duration-200 border border-[#e2e8f0]" title="{{ __('auth.back') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>

            <div class="flex flex-col items-center mb-8 select-none">
                <div class="relative mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#00A3FF] flex items-center justify-center shadow-lg shadow-[#0066FF]/25 animate-pulse-slow">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1v3M12 20v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M1 12h3M20 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" />
                        </svg>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <span class="text-xs font-bold tracking-widest text-[#0066FF] uppercase">{{ __('app.brand') }}</span>
            </div>

            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F0F7FF] border border-[#0066FF]/10 text-xs font-semibold text-[#0066FF] mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('twofactor.secure') }}
                </div>
                <h2 class="text-2xl font-extrabold text-[#0f172a] tracking-tight">{{ __('twofactor.title') }}</h2>
                <p class="text-sm text-[#64748b] mt-2 font-medium leading-relaxed">{{ __('twofactor.subtitle') }}</p>
                <div class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-[#0f172a] bg-gradient-to-r from-[#F8FAFC] to-[#F1F5F9] border border-[#e2e8f0] rounded-xl px-4 py-2">
                    <svg class="w-4 h-4 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    {{ $email }}
                </div>
            </div>

            @if(session('status'))
                <div class="mb-5 p-3.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 text-sm font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('two-factor.verify') }}" method="POST" class="space-y-5">
                @csrf
                <div class="relative">
                    <label for="code" class="block text-xs font-bold text-[#475569] uppercase tracking-wider mb-2">{{ __('twofactor.code_label') }}</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="code"
                            id="code"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="• • • • • •"
                            required
                            autofocus
                            class="w-full text-center text-2xl font-bold tracking-[0.4em] text-[#1e293b] placeholder:text-[#CBD5E1] bg-white border-2 border-[#e2e8f0] rounded-xl px-5 py-4 focus:outline-none focus:ring-4 focus:ring-[#0066FF]/10 focus:border-[#0066FF] transition-all duration-300"
                        />
                    </div>
                </div>

                <label for="trust_device" class="group flex items-start gap-3 p-3.5 rounded-xl border border-[#e2e8f0] bg-gradient-to-r from-[#F8FAFC] to-[#F1F5F9] hover:border-[#0066FF]/30 hover:from-[#F0F7FF] hover:to-[#F8FAFC] transition-all duration-200 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="trust_device"
                        id="trust_device"
                        value="1"
                        class="peer mt-0.5 w-4 h-4 accent-[#0066FF] cursor-pointer"
                    />
                    <span class="flex-1">
                        <span class="block text-sm font-bold text-[#0f172a]">{{ __('twofactor.trust_device') }}</span>
                        <span class="block text-xs text-[#64748b] font-medium mt-0.5">{{ __('twofactor.trust_device_hint', ['days' => 30]) }}</span>
                    </span>
                    <svg class="w-4 h-4 text-[#0066FF] mt-0.5 flex-shrink-0 opacity-0 group-hover:opacity-100 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </label>

                <button type="submit" class="w-full bg-gradient-to-r from-[#0066FF] to-[#0088FF] hover:from-[#0052CC] hover:to-[#0066CC] text-white rounded-xl py-3.5 px-5 text-sm font-bold tracking-wide transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#0066FF]/20 cursor-pointer flex items-center justify-center gap-2 active:scale-[0.98] shadow-lg shadow-[#0066FF]/20 hover:shadow-xl hover:shadow-[#0066FF]/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('twofactor.submit') }}
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-[#e2e8f0]/60">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[#94a3b8]">{{ __('twofactor.resend_help') }}</span>
                    <form action="{{ route('two-factor.resend-challenge') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" id="resend-btn" class="text-xs font-bold text-[#0066FF] hover:text-[#0052CC] transition-colors duration-200 disabled:text-[#CBD5E1] disabled:cursor-not-allowed">
                            {{ __('twofactor.resend') }}
                        </button>
                    </form>
                </div>
                <div class="mt-2 text-[11px] text-[#94a3b8] text-center">{{ __('twofactor.available_in') }} <span id="resend-countdown" class="font-bold text-[#0066FF]">30</span>s</div>
            </div>

            <div class="mt-5 pt-5 border-t border-[#e2e8f0]/40 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0066FF] hover:text-[#0052CC] transition-colors duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('auth.home') }}
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 102, 255, 0.3); }
            50% { box-shadow: 0 0 0 12px rgba(0, 102, 255, 0); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
    </style>

    <script>
        (function () {
            var btn = document.getElementById('resend-btn');
            var counter = document.getElementById('resend-countdown');
            if (!btn || !counter) return;

            var remaining = 30;
            btn.disabled = true;

            var timer = setInterval(function () {
                remaining--;
                counter.textContent = remaining;
                if (remaining <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    counter.parentElement.style.display = 'none';
                }
            }, 1000);
        })();
    </script>
</x-layouts.auth>

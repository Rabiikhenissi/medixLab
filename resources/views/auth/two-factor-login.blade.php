<x-layouts.auth>
    <x-slot:title>{{ __('twofactor.meta_title') }}</x-slot:title>

    <div class="w-full max-w-[458px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <a href="{{ route('home') }}" class="absolute top-6 left-6 text-[#64748b] hover:text-[#1e293b] transition-colors duration-200" title="{{ __('auth.back') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>

            <!-- Logo -->
            <div class="flex flex-col items-center mb-6 select-none">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0066FF] flex items-center justify-center shadow-sm mb-2">
                    <svg class="w-6 h-6 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20V8l8 7 8-7v12" />
                        <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0D9488" />
                    </svg>
                </div>
                <span class="text-xs font-bold tracking-widest text-[#0066FF] uppercase">Medix eSanté</span>
            </div>

            <!-- Mail icon -->
            <div class="flex justify-center mb-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#00A3FF] flex items-center justify-center shadow-lg shadow-[#0066FF]/20">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3" />
                        <path d="M2 7l9.3 6a1 1 0 0 0 1.4 0L22 7" />
                    </svg>
                </div>
            </div>

            <!-- Headings -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-semibold text-[#1e293b] tracking-tight">{{ __('twofactor.title') }}</h2>
                <p class="text-sm text-[#64748b] mt-1.5 font-medium leading-relaxed">
                    {{ __('twofactor.subtitle') }}
                </p>
                <div class="mt-2 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0f172a] bg-[#F1F5F9] border border-[#e2e8f0] rounded-lg px-3 py-1.5">
                    <svg class="w-4 h-4 text-[#0066FF]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3" />
                        <path d="M2 7l9.3 6a1 1 0 0 0 1.4 0L22 7" />
                    </svg>
                    {{ $email }}
                </div>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Code form -->
            <form action="{{ route('two-factor.verify') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="block text-xs font-semibold text-[#475569] uppercase tracking-wider mb-1.5">{{ __('twofactor.code_label') }}</label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="••••••"
                        required
                        autofocus
                        class="w-full text-center text-2xl font-bold tracking-[0.5em] text-[#1e293b] placeholder:text-[#CBD5E1] bg-white border border-[#e2e8f0] rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition-all duration-200"
                    />
                </div>

                <button type="submit" class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white rounded-xl py-2.5 px-5 text-sm font-semibold tracking-wide transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]/20 cursor-pointer flex items-center justify-center active:scale-[0.98]">
                    {{ __('twofactor.submit') }}
                </button>
            </form>

            <!-- Resend -->
            <div class="mt-4 text-center">
                <span class="text-xs text-[#94a3b8]">{{ __('twofactor.resend_help') }}</span>
                <form action="{{ route('two-factor.resend-challenge') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" id="resend-btn" class="text-xs font-semibold text-[#0066FF] hover:underline ml-1 disabled:text-[#94a3b8] disabled:hover:no-underline disabled:cursor-not-allowed">
                        {{ __('twofactor.resend') }}
                    </button>
                </form>
                <div class="mt-2 text-[11px] text-[#94a3b8]">{{ __('twofactor.available_in') }} <span id="resend-countdown" class="font-semibold text-[#475569]">30</span> s</div>
            </div>

            <!-- Footer -->
            <div class="mt-6 pt-6 border-t border-[#e2e8f0]/80 text-center text-xs text-[#64748b] leading-relaxed">
                <a href="{{ route('home') }}" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.home') }}</a>
            </div>
        </div>
    </div>

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

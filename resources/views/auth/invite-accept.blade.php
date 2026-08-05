<x-layouts.auth>
    <x-slot:title>{{ __('invite.meta_title') }}</x-slot:title>

    <x-auth-card
        title="{{ __('invite.title') }}"
        subtitle="{{ __('invite.subtitle') }} {{ $invite->roleLabel() }}"
        :badge="__('invite.badge')"
        action="{{ route('invite.accept.store', $invite->token) }}"
        backUrl="{{ route('home') }}"
    >
        @if($errors->any())
            <div class="p-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-[#64748b] mb-1.5">{{ __('auth.email') }}</label>
            <input type="email" value="{{ $invite->email }}" disabled
                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-[#e2e8f0] bg-[#f8fafc] text-[#94a3b8] focus:outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="text"
                name="first_name"
                label="{{ __('auth.first_name') }}"
                placeholder="ex. Marie"
                :required="true"
                :value="old('first_name', $invite->first_name)"
            />
            <x-input
                type="text"
                name="last_name"
                label="{{ __('auth.last_name') }}"
                placeholder="ex. Martin"
                :required="true"
                :value="old('last_name', $invite->last_name)"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-input
                type="password"
                name="password"
                label="{{ __('auth.password') }}"
                placeholder="••••••••"
                :required="true"
                :show-strength="true"
            />
            <x-input
                type="password"
                name="password_confirmation"
                label="{{ __('auth.password_confirm') }}"
                placeholder="••••••••"
                :required="true"
            />
        </div>

        <!-- Legal consents -->
        <div class="space-y-2 pt-1">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="accept_terms" value="1" {{ old('accept_terms') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-[#e2e8f0] text-[#0066FF] focus:ring-[#0066FF]/20">
                <span class="text-xs text-[#64748b] leading-relaxed">
                    {{ __('auth.terms') }} <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.terms_link') }}</a>
                </span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="accept_privacy" value="1" {{ old('accept_privacy') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-[#e2e8f0] text-[#0066FF] focus:ring-[#0066FF]/20">
                <span class="text-xs text-[#64748b] leading-relaxed">
                    {{ __('auth.privacy') }} <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.privacy_link') }}</a> {{ __('auth.privacy_rgpd') }}
                </span>
            </label>
        </div>

        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                {{ __('invite.submit') }}
            </x-button>
        </div>
    </x-auth-card>
</x-layouts.auth>

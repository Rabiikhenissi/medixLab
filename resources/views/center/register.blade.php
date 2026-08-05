<x-layouts.auth>
    <x-slot:title>{{ __('center.register_title') }} - Medix eSanté</x-slot:title>

    <x-auth-card
        title="{{ __('center.register_title') }}"
        subtitle="{{ __('center.register_subtitle') }}"
        action="{{ route('center.register') }}"
        backUrl="{{ route('center.login') }}"
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
        <!-- Center Name -->
        <x-input
            type="text"
            name="center_name"
            label="{{ __('auth.center_name') }}"
            placeholder="ex. Clinique El Amen"
            :required="true"
        />

        <!-- Responsible Person -->
        <x-input
            type="text"
            name="responsible"
            label="{{ __('auth.responsible') }}"
            placeholder="ex. Dr. Ahmed Ben Ali"
            :required="true"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="{{ __('auth.email') }}"
                placeholder="contact@centre.com"
                :required="true"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="{{ __('auth.phone') }}"
                placeholder="+216 00 000 000"
                :required="true"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- City -->
            <x-input
                type="text"
                name="city"
                label="{{ __('auth.city') }}"
                placeholder="ex. Tunis"
                :required="true"
            />

            <!-- Country -->
            <x-input
                type="select"
                name="country"
                label="{{ __('auth.country') }}"
                placeholder="{{ __('auth.select') }}"
                :required="true"
                :options="[
                    'TN' => __('country.TN'),
                    'FR' => __('country.FR'),
                    'MA' => __('country.MA'),
                    'DZ' => __('country.DZ'),
                    'autre' => __('country.autre')
                ]"
                value="TN"
            />
        </div>

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="{{ __('auth.address') }}"
            placeholder="ex. 45 Rue du Lac, Les Berges du Lac, Tunis"
            :required="false"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <x-input
                type="password"
                name="password"
                label="{{ __('auth.password') }}"
                placeholder="••••••••"
                :required="true"
                :show-strength="true"
            />

            <!-- Confirm Password -->
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

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                {{ __('register.submit') }}
            </x-button>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <a href="{{ route('center.login') }}" class="font-semibold text-[#64748b] hover:text-[#7C3AED] hover:underline">
                    {{ __('login.already') }}
                </a>
            </div>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

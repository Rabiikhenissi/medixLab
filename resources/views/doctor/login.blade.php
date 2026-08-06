<x-layouts.auth>
    <x-slot:title>{{ __('doctor.login_title') }} - {{ __('login.meta') }} - {{ __('app.brand') }}</x-slot:title>

    <x-auth-card
        title="{{ __('doctor.login_title') }}"
        subtitle="{{ __('login.subtitle') }}"
        badge="{{ __('doctor.login_badge') }}"
        action="{{ route('doctor.login') }}"
        backUrl="{{ route('home') }}"
    >
        @if(session('status'))
            <div class="p-3 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Email Field -->
        <x-input
            type="email"
            name="email"
            label="{{ __('auth.email') }}"
            placeholder="doctor@esante.com"
            :required="true"
        />

        <!-- Password Field -->
        <x-input
            type="password"
            name="password"
            label="{{ __('auth.password') }}"
            placeholder="••••••••••••"
            :required="true"
        />

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
            <x-input
                type="checkbox"
                name="remember"
                label="{{ __('login.remember') }}"
            />

           <a href="{{ route('doctor.password.request') }}" class="text-xs font-semibold text-[#0066FF] hover:underline">
    {{ __('login.forgot') }}
</a>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                {{ __('login.submit') }}
            </x-button>
        </div>

        <x-slot:footer>
            <span>{{ __('login.no_account') }} </span>
            <a href="{{ route('doctor.register') }}" class="font-semibold text-[#0066FF] hover:underline">{{ __('login.signup') }}</a>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

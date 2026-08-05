<x-layouts.auth>
    <x-slot:title>{{ __('reset.meta_title') }}</x-slot:title>

   <x-auth-card
    title="{{ __('reset.title') }}"
    subtitle="{{ __('reset.subtitle') }}"
    badge="{{ strtoupper($role) }}"
    action="{{ route($role.'.password.update') }}"
>

        @if($errors->any())
            <div class="p-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl mb-4">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <input type="hidden" name="token" value="{{ $token }}">

        <x-input
            type="email"
            name="email"
            label="{{ __('auth.email') }}"
            value="{{ $email }}"
            :required="true"
        />


        <x-input
            type="password"
            name="password"
            label="{{ __('auth.new_password') }}"
            placeholder="••••••••"
            :required="true"
        />


        <x-input
            type="password"
            name="password_confirmation"
            label="{{ __('auth.password_confirm_full') }}"
            placeholder="••••••••"
            :required="true"
        />


        <div class="pt-4">
            <x-button color="slate" :fullWidth="true">
                {{ __('reset.submit') }}
            </x-button>
        </div>


        <x-slot:footer>
            <div class="flex justify-center w-full">
                <a href="{{ route($role . '.login') }}"
                   class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    {{ __('login.back') }}
                </a>
            </div>
        </x-slot:footer>

    </x-auth-card>

</x-layouts.auth>
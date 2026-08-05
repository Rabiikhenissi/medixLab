<x-layouts.auth>
    <x-slot:title>{{ __('forgot.meta_title') }}</x-slot:title>

    <x-auth-card
        title="{{ __('forgot.title') }}"
        subtitle="{{ __('forgot.subtitle') }}"
        badge="{{ strtoupper($role) }}"
        action="{{ route($role . '.password.email') }}"
        backUrl="{{ route($role . '.login') }}"
    >

        @if(session('status'))
            <div class="p-3 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl mb-4">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <p class="text-sm text-slate-500 mb-5">
            {{ __('forgot.help') }}
        </p>


        <x-input
            type="email"
            name="email"
            label="{{ __('auth.email') }}"
            placeholder="patient@esante.com"
            :required="true"
        />


        <div class="pt-4">
            <x-button color="slate" :fullWidth="true">
                {{ __('forgot.submit') }}
            </x-button>
        </div>


        <x-slot:footer>
            <div class="flex items-center justify-center w-full">
                <a href="{{ route($role . '.login') }}"
                   class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    {{ __('login.back') }}
                </a>
            </div>
        </x-slot:footer>

    </x-auth-card>

</x-layouts.auth>

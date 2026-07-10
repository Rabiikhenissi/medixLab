```blade
<x-layouts.auth>
    <x-slot:title>Mot de passe oublié - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Mot de passe oublié"
        subtitle="Réinitialisez votre mot de passe sécurisé"
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
            Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>


        <x-input
            type="email"
            name="email"
            label="Email"
            placeholder="patient@esante.com"
            :required="true"
        />


        <div class="pt-4">
            <x-button color="slate" :fullWidth="true">
                ENVOYER LE LIEN
            </x-button>
        </div>


        <x-slot:footer>
            <div class="flex items-center justify-center w-full">
                <a href="{{ route($role . '.login') }}"
                   class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    Retour à la connexion
                </a>
            </div>
        </x-slot:footer>

    </x-auth-card>

</x-layouts.auth>
```

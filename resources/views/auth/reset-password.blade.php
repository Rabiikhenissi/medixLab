<x-layouts.auth>
    <x-slot:title>Réinitialiser le mot de passe - Medix eSanté</x-slot:title>

   <x-auth-card
    title="Nouveau mot de passe"
    subtitle="Choisissez un nouveau mot de passe sécurisé"
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
            label="Email"
            value="{{ $email }}"
            :required="true"
        />


        <x-input
            type="password"
            name="password"
            label="Nouveau mot de passe"
            placeholder="••••••••"
            :required="true"
        />


        <x-input
            type="password"
            name="password_confirmation"
            label="Confirmation du mot de passe"
            placeholder="••••••••"
            :required="true"
        />


        <div class="pt-4">
            <x-button color="slate" :fullWidth="true">
                RÉINITIALISER
            </x-button>
        </div>


        <x-slot:footer>
            <div class="flex justify-center w-full">
                <a href="{{ route($role . '.login') }}"
                   class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    Retour à la connexion
                </a>
            </div>
        </x-slot:footer>

    </x-auth-card>

</x-layouts.auth>
<x-layouts.auth>
    <x-slot:title>Vérification en deux étapes - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Vérification en deux étapes"
        subtitle="Entrez le code à 6 chiffres généré par votre application d'authentification"
        badge="SÉCURITÉ"
        action="{{ route('two-factor.verify') }}"
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

        <x-input
            type="text"
            name="code"
            label="Code à 6 chiffres"
            placeholder="••••••"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            :required="true"
            autofocus
        />

        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                VÉRIFIER
            </x-button>
        </div>
    </x-auth-card>
</x-layouts.auth>

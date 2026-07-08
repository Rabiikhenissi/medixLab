<x-layouts.auth>
    <x-slot:title>Espace Médecin - Connexion - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Espace Médecin"
        subtitle="Accédez à votre espace sécurisé"
        badge="PROFESSIONNEL DE SANTÉ"
        action="{{ route('doctor.login') }}"
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
        <!-- Email Field -->
        <x-input
            type="email"
            name="email"
            label="Email"
            placeholder="doctor@esante.com"
            :required="true"
        />

        <!-- Password Field -->
        <x-input
            type="password"
            name="password"
            label="Password"
            placeholder="••••••••••••"
            :required="true"
        />

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
            <x-input
                type="checkbox"
                name="remember"
                label="Se souvenir de moi"
            />

            <a href="#" class="text-xs font-semibold text-[#0066FF] hover:underline">
                Mot de passe oublié ?
            </a>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                SE CONNECTER
            </x-button>
        </div>

        <x-slot:footer>
            <span>Vous n'avez pas de compte ? </span>
            <a href="{{ route('doctor.register') }}" class="font-semibold text-[#0066FF] hover:underline">S'inscrire</a>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

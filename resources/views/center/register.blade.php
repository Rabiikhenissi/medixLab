<x-layouts.auth>
    <x-slot:title>Inscription Centre Médical - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Inscription Centre"
        subtitle="Créez votre compte d'établissement"
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
            label="Nom du centre"
            placeholder="ex. Clinique El Amen"
            :required="true"
        />

        <!-- Responsible Person -->
        <x-input
            type="text"
            name="responsible"
            label="Responsable"
            placeholder="ex. Dr. Ahmed Ben Ali"
            :required="true"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="Email"
                placeholder="contact@centre.com"
                :required="true"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="Téléphone"
                placeholder="+216 00 000 000"
                :required="true"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- City -->
            <x-input
                type="text"
                name="city"
                label="Ville"
                placeholder="ex. Tunis"
                :required="true"
            />

            <!-- Country -->
            <x-input
                type="select"
                name="country"
                label="Pays"
                placeholder="Sélectionner"
                :required="true"
                :options="[
                    'TN' => 'Tunisie',
                    'FR' => 'France',
                    'MA' => 'Maroc',
                    'DZ' => 'Algérie',
                    'autre' => 'Autre'
                ]"
                value="TN"
            />
        </div>

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="Adresse"
            placeholder="ex. 45 Rue du Lac, Les Berges du Lac, Tunis"
            :required="false"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <x-input
                type="password"
                name="password"
                label="Mot de passe"
                placeholder="••••••••"
                :required="true"
                :show-strength="true"
            />

            <!-- Confirm Password -->
            <x-input
                type="password"
                name="password_confirmation"
                label="Confirmation"
                placeholder="••••••••"
                :required="true"
            />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                S'INSCRIRE
            </x-button>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <a href="{{ route('center.login') }}" class="font-semibold text-[#64748b] hover:text-[#7C3AED] hover:underline">
                    Déjà inscrit ?
                </a>
            </div>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

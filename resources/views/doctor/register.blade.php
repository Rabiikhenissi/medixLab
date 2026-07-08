<x-layouts.auth>
    <x-slot:title>Inscription Médecin - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Inscription Médecin"
        subtitle="Créez votre compte pour accéder aux services"
        action="{{ route('doctor.register') }}"
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
        <!-- Grid container for multi-column inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- First Name -->
            <x-input
                type="text"
                name="first_name"
                label="Prénom"
                placeholder="ex. Jean"
                :required="true"
            />

            <!-- Last Name -->
            <x-input
                type="text"
                name="last_name"
                label="Nom de famille"
                placeholder="ex. Dupont"
                :required="true"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Date of Birth -->
            <x-input
                type="date"
                name="birth_date"
                label="Date de naissance"
                :required="true"
            />

            <!-- Gender -->
            <x-input
                type="select"
                name="gender"
                label="Genre"
                placeholder="Sélectionner"
                :required="true"
                :options="['M' => 'Masculin', 'F' => 'Féminin', 'O' => 'Autre']"
            />
        </div>

        <!-- CNOM ID -->
        <x-input
            type="text"
            name="cnom_id"
            label="ID Docteur (CNOM)"
            placeholder="ex. DOC12345"
            :required="true"
        />

        <!-- Specialty -->
        <x-input
            type="select"
            name="specialty"
            label="Spécialité"
            placeholder="Sélectionner une spécialité"
            :required="true"
            :options="[
                'generaliste' => 'Médecin Généraliste',
                'cardiologue' => 'Cardiologue',
                'pediatre' => 'Pédiatre',
                'dermatologue' => 'Dermatologue',
                'gynecologue' => 'Gynécologue',
                'autre' => 'Autre spécialité'
            ]"
        />

        <!-- Email -->
        <x-input
            type="email"
            name="email"
            label="Email"
            placeholder="doctor@esante.com"
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

        <!-- Country -->
        <x-input
            type="select"
            name="country"
            label="Pays"
            placeholder="Sélectionner"
            :required="true"
            :options="[
                'TN' => 'TN Tunisie',
                'FR' => 'FR France',
                'MA' => 'MA Maroc',
                'DZ' => 'DZ Algérie',
                'autre' => 'Autre Pays'
            ]"
            value="TN"
        />

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="Adresse"
            placeholder="ex. Rue de la Liberté, Tunis"
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
            />

            <!-- Confirm Password -->
            <x-input
                type="password"
                name="password_confirmation"
                label="Confirmer"
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
                <a href="{{ route('doctor.login') }}" class="font-semibold text-[#64748b] hover:text-[#0066FF] hover:underline">
                    Déjà inscrit ?
                </a>
            </div>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

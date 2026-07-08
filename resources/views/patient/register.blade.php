<x-layouts.auth>
    <x-slot:title>Inscription Patient - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Inscription Patient"
        subtitle="Créez votre compte pour accéder aux services"
        action="{{ route('patient.register') }}"
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
                placeholder="ex. Marie"
                :required="true"
            />

            <!-- Last Name -->
            <x-input
                type="text"
                name="last_name"
                label="Nom"
                placeholder="ex. Martin"
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
                label="Sexe"
                placeholder="Sélectionner"
                :required="true"
                :options="['M' => 'Masculin', 'F' => 'Féminin', 'O' => 'Autre']"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="Email"
                placeholder="patient@esante.com"
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

            <!-- Blood Group -->
            <x-input
                type="select"
                name="blood_group"
                label="Groupe sanguin"
                placeholder="Sélectionner"
                :required="true"
                :options="[
                    'A+' => 'A+',
                    'A-' => 'A-',
                    'B+' => 'B+',
                    'B-' => 'B-',
                    'AB+' => 'AB+',
                    'AB-' => 'AB-',
                    'O+' => 'O+',
                    'O-' => 'O-'
                ]"
            />
        </div>

        <!-- Allergies (optional) -->
        <x-input
            type="text"
            name="allergies"
            label="Allergies (Optionnel)"
            placeholder="ex. Pénicilline, Pollen (sinon laisser vide)"
            :required="false"
        />

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="Adresse"
            placeholder="ex. Avenue Habib Bourguiba, Tunis"
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
                <a href="{{ route('patient.login') }}" class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    Déjà inscrit ?
                </a>
            </div>
        </x-slot:footer>
    </x-auth-card>
</x-layouts.auth>

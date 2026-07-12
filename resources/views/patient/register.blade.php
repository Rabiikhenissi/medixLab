<x-layouts.auth>
    <x-slot:title>Inscription Patient - Medix eSanté</x-slot:title>

    <x-auth-card
        title="Inscription Patient"
        subtitle="Créez votre compte pour accéder aux services"
        action="{{ route('patient.register') }}"
        backUrl="{{ route('patient.login') }}"
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
                :value="old('first_name')"
            />

            <!-- Last Name -->
            <x-input
                type="text"
                name="last_name"
                label="Nom"
                placeholder="ex. Martin"
                :required="true"
                :value="old('last_name')"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Date of Birth -->
            <x-input
                type="date"
                name="birth_date"
                label="Date de naissance"
                :required="true"
                :value="old('birth_date')"
            />

            <!-- Gender -->
            <x-input
                type="select"
                name="gender"
                label="Sexe"
                placeholder="Sélectionner"
                :required="true"
                :options="['M' => 'Masculin', 'F' => 'Féminin', 'O' => 'Autre']"
                :value="old('gender')"
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
                :value="old('email')"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="Téléphone"
                placeholder="+216 00 000 000"
                :required="true"
                :value="old('phone')"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Country -->
            <x-input
                type="select"
                name="country"
                label="Pays"
                placeholder="Chargement des pays..."
                :required="true"
                :options="[]"
            />

            <!-- State/Province -->
            <x-input
                type="select"
                name="state_code"
                label="Province / État"
                placeholder="Sélectionner un pays d'abord"
                :required="true"
                :options="[]"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                :value="old('blood_group')"
            />

            <!-- Allergies (optional) -->
            <x-input
                type="text"
                name="allergies"
                label="Allergies (Optionnel)"
                placeholder="ex. Pénicilline, Pollen (sinon laisser vide)"
                :required="false"
                :value="old('allergies')"
            />
        </div>

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="Adresse"
            placeholder="ex. Avenue Habib Bourguiba, Tunis"
            :required="false"
            :value="old('address')"
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.getElementById('country');
            const stateSelect = document.getElementById('state_code');
            
            const oldCountry = @json(old('country', 'TN'));
            const oldStateCode = @json(old('state_code'));

            function loadCountries() {
                countrySelect.innerHTML = '<option value="" disabled selected>Chargement des pays...</option>';
                
                fetch('{{ route("countries.index") }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(res => {
                        countrySelect.innerHTML = '<option value="" disabled>Sélectionner un pays</option>';
                        
                        if (res.data && res.data.length > 0) {
                            res.data.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.Iso2;
                                option.textContent = item.name;
                                countrySelect.appendChild(option);
                            });

                            const valueToSelect = oldCountry || 'TN';
                            if (valueToSelect) {
                                countrySelect.value = valueToSelect;
                                if (countrySelect.value === valueToSelect) {
                                    loadStates(valueToSelect);
                                } else {
                                    countrySelect.value = "";
                                }
                            }
                        } else {
                            countrySelect.innerHTML = '<option value="" disabled selected>Aucun pays disponible</option>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching countries:', err);
                        countrySelect.innerHTML = '<option value="" disabled selected>Erreur de chargement des pays</option>';
                    });
            }

           function loadStates(countryCode) {

    console.log("Loading states for country:", countryCode);

    stateSelect.innerHTML =
        '<option value="" disabled selected>Chargement des provinces...</option>';

    fetch(`/countries/${countryCode}/states`)
        .then(response => {

            console.log("States HTTP status:", response.status);

            return response.json();

        })
        .then(res => {

            console.log("States response:", res);


            stateSelect.innerHTML =
                '<option value="" disabled selected>Sélectionner une province</option>';


            if (res.data && res.data.length > 0) {

                res.data.forEach(item => {

                    console.log("Adding state:", item);


                    const option = document.createElement('option');

                    option.value = item.state_code;
                    option.textContent = item.name;

                    stateSelect.appendChild(option);

                });


            } else {

                console.log("No states returned");

                stateSelect.innerHTML =
                '<option value="" disabled>Aucune province disponible</option>';

            }

        })
        .catch(err => {

            console.error("States fetch error:", err);

            stateSelect.innerHTML =
            '<option value="" disabled>Erreur de chargement</option>';

        });
}

            countrySelect.addEventListener('change', function () {
                const countryCode = this.value;
                if (countryCode) {
                    loadStates(countryCode);
                } else {
                    stateSelect.innerHTML = '<option value="" disabled selected>Sélectionner un pays d\'abord</option>';
                }
            });

            // Initial load
            loadCountries();
        });
    </script>
</x-layouts.auth>

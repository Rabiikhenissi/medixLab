<x-layouts.auth>
    <x-slot:title>{{ __('patient.register_title') }} - Medix eSanté</x-slot:title>

    <x-auth-card
        title="{{ __('patient.register_title') }}"
        subtitle="{{ __('patient.register_subtitle') }}"
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
                label="{{ __('auth.first_name') }}"
                placeholder="ex. Marie"
                :required="true"
                :value="old('first_name')"
            />

            <!-- Last Name -->
            <x-input
                type="text"
                name="last_name"
                label="{{ __('auth.last_name') }}"
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
                label="{{ __('auth.date_of_birth') }}"
                :required="true"
                :value="old('birth_date')"
            />

            <!-- Gender -->
            <x-input
                type="select"
                name="gender"
                label="{{ __('auth.gender_sex') }}"
                placeholder="{{ __('auth.select') }}"
                :required="true"
                :options="['M' => __('auth.gender_m'), 'F' => __('auth.gender_f'), 'O' => __('auth.gender_o')]"
                :value="old('gender')"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="{{ __('auth.email') }}"
                placeholder="patient@esante.com"
                :required="true"
                :value="old('email')"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="{{ __('auth.phone') }}"
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
                label="{{ __('auth.country') }}"
                placeholder="{{ __('register.loading_countries') }}"
                :required="true"
                :options="[]"
            />

            <!-- State/Province -->
            <x-input
                type="select"
                name="state_code"
                label="{{ __('auth.state') }}"
                placeholder="{{ __('register.select_country_first') }}"
                :required="true"
                :options="[]"
            />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Blood Group -->
            <x-input
                type="select"
                name="blood_group"
                label="{{ __('auth.blood_group') }}"
                placeholder="{{ __('auth.select') }}"
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
                label="{{ __('auth.allergies') }}"
                placeholder="ex. Pénicilline, Pollen (sinon laisser vide)"
                :required="false"
                :value="old('allergies')"
            />
        </div>

        <!-- Address -->
        <x-input
            type="textarea"
            name="address"
            label="{{ __('auth.address') }}"
            placeholder="ex. Avenue Habib Bourguiba, Tunis"
            :required="false"
            :value="old('address')"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <x-input
                type="password"
                name="password"
                label="{{ __('auth.password') }}"
                placeholder="••••••••"
                :required="true"
                :show-strength="true"
            />

            <!-- Confirm Password -->
            <x-input
                type="password"
                name="password_confirmation"
                label="{{ __('auth.password_confirm') }}"
                placeholder="••••••••"
                :required="true"
            />
        </div>

        <!-- Legal consents -->
        <div class="space-y-2 pt-1">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="accept_terms" value="1" {{ old('accept_terms') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-[#e2e8f0] text-[#0066FF] focus:ring-[#0066FF]/20">
                <span class="text-xs text-[#64748b] leading-relaxed">
                    {{ __('auth.terms') }} <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.terms_link') }}</a>
                </span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="accept_privacy" value="1" {{ old('accept_privacy') ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-[#e2e8f0] text-[#0066FF] focus:ring-[#0066FF]/20">
                <span class="text-xs text-[#64748b] leading-relaxed">
                    {{ __('auth.privacy') }} <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" class="font-semibold text-[#0066FF] hover:underline">{{ __('auth.privacy_link') }}</a> {{ __('auth.privacy_rgpd') }}
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-button color="slate" :fullWidth="true">
                {{ __('register.submit') }}
            </x-button>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                <a href="{{ route('patient.login') }}" class="font-semibold text-[#64748b] hover:text-[#0D9488] hover:underline">
                    {{ __('login.already') }}
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
                countrySelect.innerHTML = '<option value="" disabled selected>{{ __('register.loading_countries') }}</option>';
                
                fetch('{{ route("countries.index") }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(res => {
                        countrySelect.innerHTML = '<option value="" disabled>{{ __('register.select_country') }}</option>';
                        
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
                            countrySelect.innerHTML = '<option value="" disabled selected>{{ __('register.no_countries') }}</option>';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching countries:', err);
                        countrySelect.innerHTML = '<option value="" disabled selected>{{ __('register.countries_error') }}</option>';
                    });
            }

           function loadStates(countryCode) {

    console.log("Loading states for country:", countryCode);

    stateSelect.innerHTML =
        '<option value="" disabled selected>{{ __('register.loading_states') }}</option>';

    fetch(`/countries/${countryCode}/states`)
        .then(response => {

            console.log("States HTTP status:", response.status);

            return response.json();

        })
        .then(res => {

            console.log("States response:", res);


            stateSelect.innerHTML =
                '<option value="" disabled selected>{{ __('register.select_state') }}</option>';


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
                '<option value="" disabled>{{ __('register.no_states') }}</option>';

            }

        })
        .catch(err => {

            console.error("States fetch error:", err);

            stateSelect.innerHTML =
            '<option value="" disabled>{{ __('register.states_error') }}</option>';

        });
}

            countrySelect.addEventListener('change', function () {
                const countryCode = this.value;
                if (countryCode) {
                    loadStates(countryCode);
                } else {
                    stateSelect.innerHTML = '<option value="" disabled selected>{{ __('register.select_country_first') }}</option>';
                }
            });

            // Initial load
            loadCountries();
        });
    </script>
</x-layouts.auth>

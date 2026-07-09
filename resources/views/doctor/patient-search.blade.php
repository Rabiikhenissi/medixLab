<x-layouts.auth>
    <x-slot:title>Rechercher un Patient - Medix eSanté</x-slot:title>

    <div class="w-full max-w-[620px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#e2e8f0]/80">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#1e293b]">Rechercher un Patient</h2>
                        <p class="text-xs text-[#64748b] mt-0.5">Scannez le QR ou entrez le code patient</p>
                    </div>
                </div>
                <a
                    href="{{ route('doctor.dashboard') }}"
                    class="inline-flex items-center gap-2 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold px-4 py-2 rounded-xl transition text-xs uppercase tracking-wider border border-[#e2e8f0]"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Tableau de Bord
                </a>
            </div>

            {{-- Alert Messages --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Erreur</h3>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-700 mt-1">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Search Form --}}
            <form id="patientSearchForm" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#1e293b] mb-2 uppercase tracking-wider">Code Patient</label>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input
                            type="text"
                            name="patient_code"
                            id="patient_code"
                            placeholder="Entrez ou scannez le code patient"
                            class="w-full pl-12 pr-4 py-3 border border-[#e2e8f0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066FF]/20 focus:border-[#0066FF] transition text-[#1e293b] bg-[#F8FAFC]"
                            autocomplete="off"
                            required
                        />
                    </div>
                    <p class="text-xs text-[#94a3b8] mt-2">Utilisez votre lecteur QR ou entrez le code directement</p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        class="flex-1 bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-sm flex items-center justify-center gap-2 uppercase tracking-wider text-sm"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Rechercher
                    </button>
                    <a
                        href="{{ route('doctor.dashboard') }}"
                        class="inline-flex items-center gap-2 bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] text-sm uppercase tracking-wider border border-[#e2e8f0]"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Retour
                    </a>
                </div>
            </form>

            {{-- Loading --}}
            <div id="loadingIndicator" class="hidden mt-6 flex items-center justify-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-[#0066FF] border-t-transparent"></div>
                <span class="text-sm font-semibold text-[#0066FF]">Recherche en cours...</span>
            </div>

            {{-- Patient Found Section --}}
            <div id="patientFoundSection" class="hidden mt-8 pt-6 border-t border-[#e2e8f0]">

                {{-- Patient Card --}}
                <div class="bg-gradient-to-br from-[#F8FAFC] to-[#EFF6FF] border border-[#e2e8f0]/60 rounded-2xl p-5 mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0066FF] to-[#0052CC] flex items-center justify-center text-white font-bold text-2xl flex-shrink-0 shadow-md">
                            <span id="patientAvatar">P</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 id="patientName" class="text-lg font-bold text-[#1e293b] truncate">—</h4>
                            <p id="patientCodeBadge" class="text-xs text-[#0066FF] font-mono bg-[#0066FF]/10 px-2 py-0.5 rounded-md inline-block mb-1 border border-[#0066FF]/20">—</p>
                            <p id="patientEmail" class="text-sm text-[#64748b] truncate"></p>
                            <p id="patientPhone" class="text-sm text-[#64748b]"></p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div id="accessSection" class="space-y-3">
                    <button
                        type="button"
                        id="requestAccessBtn"
                        class="w-full bg-[#0066FF] hover:bg-[#0052CC] text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-sm flex items-center justify-center gap-2 uppercase tracking-wider text-sm"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        Demander l'Accès au Patient
                    </button>

                    <div id="accessGrantedMessage" class="hidden p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-emerald-800">Accès autorisé ✓</p>
                                <p class="text-xs text-emerald-700 mt-0.5">Vous pouvez maintenant prescrire des examens.</p>
                            </div>
                        </div>
                    </div>

                    <div id="accessPendingMessage" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-bold text-amber-800">En attente de confirmation</p>
                                <p class="text-xs text-amber-700 mt-0.5">Votre demande a été envoyée au patient.</p>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        id="proceedToExamsBtn"
                        class="hidden w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-sm flex items-center justify-center gap-2 uppercase tracking-wider text-sm"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Prescrire des Examens
                    </button>

                    <button
                        type="button"
                        id="clearSearchBtn"
                        class="w-full bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#64748b] font-bold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 uppercase tracking-wider text-sm border border-[#e2e8f0]"
                    >
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Nouvelle Recherche
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form              = document.getElementById('patientSearchForm');
        const patientCodeInput  = document.getElementById('patient_code');
        const loadingIndicator  = document.getElementById('loadingIndicator');
        const patientFoundSection = document.getElementById('patientFoundSection');
        const patientNameEl     = document.getElementById('patientName');
        const patientCodeBadge  = document.getElementById('patientCodeBadge');
        const patientEmailEl    = document.getElementById('patientEmail');
        const patientPhoneEl    = document.getElementById('patientPhone');
        const patientAvatarEl   = document.getElementById('patientAvatar');
        const requestAccessBtn  = document.getElementById('requestAccessBtn');
        const accessGrantedMsg  = document.getElementById('accessGrantedMessage');
        const accessPendingMsg  = document.getElementById('accessPendingMessage');
        const proceedToExamsBtn = document.getElementById('proceedToExamsBtn');
        const clearSearchBtn    = document.getElementById('clearSearchBtn');

        let currentPatientId = null;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await searchPatient();
        });

        async function searchPatient() {
            const code = patientCodeInput.value.trim();
            if (!code) return;

            loadingIndicator.classList.remove('hidden');
            patientFoundSection.classList.add('hidden');

            try {
                const response = await fetch('{{ route('doctor.search-patient') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ patient_code: code }),
                });

                const data = await response.json();

                if (data.success) {
                    currentPatientId = data.patient.id;

                    const firstName = data.patient.user.first_name;
                    const lastName  = data.patient.user.last_name;
                    const initials  = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();

                    patientAvatarEl.textContent  = initials;
                    patientNameEl.textContent    = `${firstName} ${lastName}`;
                    patientCodeBadge.textContent = `Code: ${data.patient.patient_code}`;
                    patientEmailEl.textContent   = `✉ ${data.patient.user.email}`;
                    patientPhoneEl.textContent   = `☎ ${data.patient.user.phone}`;

                    // Reset state
                    requestAccessBtn.classList.remove('hidden');
                    requestAccessBtn.disabled = false;
                    proceedToExamsBtn.classList.add('hidden');
                    accessGrantedMsg.classList.add('hidden');
                    accessPendingMsg.classList.add('hidden');

                    if (data.access_status === 'granted') {
                        requestAccessBtn.classList.add('hidden');
                        accessGrantedMsg.classList.remove('hidden');
                        proceedToExamsBtn.classList.remove('hidden');
                        proceedToExamsBtn.onclick = () => {
                            window.location.href = `{{ route('doctor.select-exams', '') }}/${currentPatientId}`;
                        };
                    } else if (data.access_status === 'pending') {
                        requestAccessBtn.classList.add('hidden');
                        accessPendingMsg.classList.remove('hidden');
                    }

                    patientFoundSection.classList.remove('hidden');
                } else {
                    showToast(data.message || 'Patient non trouvé', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Une erreur est survenue lors de la recherche.', 'error');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        }

        requestAccessBtn.addEventListener('click', async () => {
            requestAccessBtn.disabled = true;
            requestAccessBtn.innerHTML = '<svg class="w-5 h-5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Envoi en cours...';

            try {
                const response = await fetch('{{ route('doctor.request-access') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ patient_id: currentPatientId }),
                });

                const data = await response.json();

                if (data.success) {
                    if (data.access_granted) {
                        requestAccessBtn.classList.add('hidden');
                        accessGrantedMsg.classList.remove('hidden');
                        proceedToExamsBtn.classList.remove('hidden');
                        proceedToExamsBtn.onclick = () => {
                            window.location.href = `{{ route('doctor.select-exams', '') }}/${currentPatientId}`;
                        };
                        showToast('Accès autorisé !', 'success');
                    } else {
                        requestAccessBtn.classList.add('hidden');
                        accessPendingMsg.classList.remove('hidden');
                        showToast('Demande envoyée au patient', 'success');
                    }
                } else {
                    if (data.message && data.message.includes('déjà')) {
                        requestAccessBtn.classList.add('hidden');
                        accessPendingMsg.classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Erreur', 'error');
                        requestAccessBtn.disabled = false;
                        requestAccessBtn.innerHTML = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg> Demander l\'Accès au Patient';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Une erreur est survenue.', 'error');
                requestAccessBtn.disabled = false;
            }
        });

        clearSearchBtn.addEventListener('click', () => {
            patientCodeInput.value = '';
            patientFoundSection.classList.add('hidden');
            currentPatientId = null;
            requestAccessBtn.disabled = false;
            requestAccessBtn.innerHTML = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg> Demander l\'Accès au Patient';
            patientCodeInput.focus();
        });

        function showToast(message, type) {
            const el = document.createElement('div');
            el.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-emerald-500' : 'bg-red-500'} text-white px-5 py-3 rounded-xl shadow-xl z-50 flex items-center gap-2 text-sm font-semibold`;
            el.innerHTML = `<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span>${message}</span>`;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }
    </script>
</x-layouts.auth>

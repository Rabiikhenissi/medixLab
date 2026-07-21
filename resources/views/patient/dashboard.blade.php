<x-layouts.patient>
    <x-slot:title>{{ __('Tableau de bord Patient - Medix eSanté') }}</x-slot:title>

    @section('content')
    <div class="w-full max-w-[1400px] mx-auto">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <!-- Header -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div
                        class="w-12 h-12 rounded-full bg-white border-2 border-[#0D9488] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#0D9488]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20V8l8 7 8-7v12" />
                            <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0D9488" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#1e293b]">{{ __('Espace Patient') }}</h2>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 uppercase mt-1">
                            PATIENT
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notificationBell" type="button"
                            class="relative p-2 text-[#64748b] hover:text-[#0D9488] transition" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notificationBadge"
                                class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">0</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notificationPanel"
                            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-[#e2e8f0] z-50 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#0D9488] to-[#0a7068] p-4 text-white flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-sm">{{ __('Notifications') }}</h3>
                                    <p class="text-[10px] text-[#ffffff]/90">{!! __('Vous avez :count notification(s)', ['count' => '<span id="unreadNotifCount">0</span>']) !!}</p>
                                </div>
                                <button id="markAllReadBtn" type="button" class="text-[10px] font-bold underline text-white hover:text-teal-200 transition cursor-pointer">
                                    {{ __('Tout marquer lu') }}
                                </button>
                            </div>

                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications loaded here -->
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">{{ __('Chargement des notifications...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('profile.show') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 hover:bg-[#0D9488] hover:text-white transition uppercase tracking-wider"
                       title="Mon profil">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Profil
                    </a>
                    <a href="{{ route('patient.analytics') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 hover:bg-[#0D9488] hover:text-white transition uppercase tracking-wider"
                       title="Mes statistiques">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/>
                        </svg>
                        Stats
                    </a>
                    <a href="{{ route('patient.medical-history') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 hover:bg-[#0D9488] hover:text-white transition uppercase tracking-wider"
                       title="Voir l'historique médical complet">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historique
                    </a>
                    <form action="{{ route('patient.logout') }}" method="POST">
                        @csrf
                        <x-button type="submit" color="slate" :fullWidth="false" class="!py-1.5 !px-4 !text-xs">
                            {{ __('SE DÉCONNECTER') }}
                        </x-button>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Greeting -->
                <div>
                    <h1 class="text-2xl font-bold text-[#1e293b]">
                        {{ __('Bonjour, ') }}<span class="text-[#0D9488]">{{ $user->first_name }} {{ $user->last_name }}</span> !
                    </h1>
                    <p class="text-sm text-[#64748b] mt-1 font-medium leading-relaxed">
                        {{ __("Bienvenue sur votre espace patient sécurisé. Vous pouvez consulter vos demandes d'examens et informations personnelles.") }}
                    </p>
                </div>

                <!-- Two Column Layout for Wider View and Less Vertical Scrolling -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-4 items-start">
                    
                    <!-- Left Column: Patient Profile & Medical Activity (lg:col-span-4) -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Profile Info -->
                        <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-4 h-4 text-[#0D9488] mr-2" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                {{ __('Informations Personnelles') }}
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                    <span class="text-[#64748b] font-medium">{{ __('Code Unique Patient :') }}</span>
                                    <span class="font-bold text-[#1e293b]">{{ $user->patient->patient_code }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                    <span class="text-[#64748b] font-medium">{{ __('Adresse Email :') }}</span>
                                    <span class="font-semibold text-[#1e293b]">{{ $user->email }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                    <span class="text-[#64748b] font-medium">{{ __('Téléphone :') }}</span>
                                    <span class="font-semibold text-[#1e293b]">{{ $user->phone }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-[#64748b] font-medium">{{ __('Adresse :') }}</span>
                                    <span
                                        class="font-semibold text-[#1e293b]">{{ $user->address ?? __('Non renseignée') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Activity -->
                        <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-4 h-4 text-[#0D9488] mr-2" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Activité Médicale') }}
                            </h3>
                            <div class="space-y-4">
                                <div
                                    class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-[#0D9488]/10 flex items-center justify-center text-[#0D9488] font-bold text-sm">
                                            {{ $user->patient->examRequests()->count() }}
                                        </div>
                                        <span class="text-xs font-semibold text-[#64748b]">{{ __("Demandes d'analyses") }}</span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-[#0D9488]/10 flex items-center justify-center text-[#0D9488] font-bold text-sm">
                                            {{ $user->patient->doctorAccesses()->count() }}
                                        </div>
                                        <span class="text-xs font-semibold text-[#64748b]">{{ __('Médecins autorisés') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Requests & Analyses (lg:col-span-8) -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Pending Access Requests Section -->
                        <div class="bg-[#F8FAFC]/30 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                                <svg class="w-5 h-5 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.354a4 4 0 110 5.292M15 10H9m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __("Demandes d'Accès en Attente") }}
                            </h3>
                            <div id="accessRequestsList" class="space-y-3">
                                <!-- Access requests loaded here -->
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">{{ __('Chargement des demandes...') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Granted Doctors Section -->
                        <div class="bg-[#F8FAFC]/30 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                                <svg class="w-5 h-5 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('Médecins Autorisés') }}
                            </h3>
                            <div id="grantedDoctorsList" class="space-y-3">
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">{{ __('Chargement...') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Blocked Doctors Section -->
                        <div class="bg-[#F8FAFC]/30 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                {{ __('Médecins Bloqués') }}
                            </h3>
                            <div id="blockedList" class="space-y-3">
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">{{ __('Chargement...') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Requests Section -->
                        <div class="bg-[#F8FAFC]/30 border border-[#e2e8f0]/60 rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                                <svg class="w-5 h-5 text-[#0D9488] mr-2" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __("Vos Demandes d'Analyses") }}
                            </h3>
                            <div id="examRequestsList" class="grid grid-cols-1 gap-4">
                                <!-- Exam requests loaded here -->
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">{{ __('Chargement des demandes...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details Modal -->
    <div id="examDetailsModal"
        class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-[600px] w-full max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#e2e8f0]/80">
                    <h3 class="text-lg font-bold text-[#1e293b]">{{ __('Détails de la Demande') }}</h3>
                    <button type="button" class="closeExamModal text-[#94a3b8] hover:text-[#1e293b]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Doctor Info -->
                <div class="mb-6 p-4 bg-[#0D9488]/10 border border-[#0D9488]/20 rounded-xl">
                    <p class="text-xs font-bold text-[#0D9488] uppercase tracking-wider mb-2">{{ __('Prescrit par') }}</p>
                    <p id="modalDoctorName" class="text-lg font-bold text-[#1e293b]"></p>
                    <p id="modalDoctorSpeciality" class="text-sm text-[#64748b]"></p>
                    <p id="modalDoctorPhone" class="text-sm text-[#64748b] mt-2"><strong>Tel:</strong> <span
                            id="phoneValue"></span></p>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-2">{{ __('Statut') }}</p>
                    <span id="modalStatus"
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 uppercase"></span>
                </div>

                <!-- Clinical Notes -->
                <div id="clinicalNotesSection" class="mb-6 hidden">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-2">{{ __('Notes Cliniques') }}</p>
                    <div id="modalClinicalNotes"
                        class="p-3 bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-xl text-sm text-[#64748b]"></div>
                </div>

                <!-- Exams List -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-3">{{ __('Examens Prescrits') }}</p>
                    <div id="modalExamsList" class="space-y-3">
    <!-- Exams will be loaded here -->
</div>
<!-- Doctor Interpretation Section (visible after approval) -->
<div id="modalDoctorInterpretationSection" class="mt-4 hidden">
    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-2">{{ __('Interprétation du Médecin') }}</p>
    <div id="modalDoctorInterpretation" class="p-3 bg-purple-50/50 border border-[#7C3AED]/10 rounded-lg text-sm text-[#475569]"></div>
</div>
                </div>

                <!-- Close + Print Buttons -->
                <div class="flex gap-3">
                    <button type="button" id="modalPrintBtn"
                        class="flex-1 flex items-center justify-center gap-2 bg-[#F8FAFC] hover:bg-[#e2e8f0] border border-[#e2e8f0] text-[#475569] font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Exporter PDF
                    </button>
                    <button type="button"
                        class="closeExamModal flex-1 bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-sm">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const notificationBell = document.getElementById('notificationBell');
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationList = document.getElementById('notificationList');
        const unreadNotifCount = document.getElementById('unreadNotifCount');
        const notificationBadge = document.getElementById('notificationBadge');
        const examRequestsList = document.getElementById('examRequestsList');
        const examDetailsModal = document.getElementById('examDetailsModal');

        // Toggle notification panel
        notificationBell.addEventListener('click', () => {
            notificationPanel.classList.toggle('hidden');
            if (!notificationPanel.classList.contains('hidden')) {
                loadNotifications();
            }
        });

        // Close notification panel when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBell.contains(e.target) && !notificationPanel.contains(e.target)) {
                notificationPanel.classList.add('hidden');
            }
        });

        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch('{{ route('patient.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadNotifications();
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            });
        }

        // Load and display notifications
        async function loadNotifications() {
            try {
                const response = await fetch('{{ route('patient.get-notifications') }}');
                const data = await response.json();

                    if (data.success && data.notifications.length > 0) {
                    notificationList.innerHTML = data.notifications.map(notif => {
                        const isAccessRequest = notif.type === 'access_request';
                        const showActions = isAccessRequest && !notif.is_read;

                        let actions = '';
                        if (showActions) {
                            actions = `
                                <div class="flex gap-2 mt-3">
                                    <button class="flex-1 text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1.5 rounded font-bold notif-accept-btn" data-access-id="${notif.reference_id}" data-notif-id="${notif.id}">
                                        ✓ Accepter
                                    </button>
                                    <button class="flex-1 text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1.5 rounded font-bold notif-decline-btn" data-access-id="${notif.reference_id}" data-notif-id="${notif.id}">
                                        ✗ Refuser
                                    </button>
                                </div>
                            `;
                        }

                        return `
                            <div class="border-b border-[#e2e8f0]/50 p-4 hover:bg-[#F8FAFC]/50 transition ${!notif.is_read ? 'bg-[#0D9488]/5' : ''}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-sm text-[#1e293b]">${notif.title}</h4>
                                        <p class="text-xs text-[#64748b] mt-1">${notif.message}</p>
                                        <p class="text-xs text-[#94a3b8] mt-2">${notif.created_at}</p>
                                    </div>
                                    ${!notif.is_read ? '<div class="w-2 h-2 bg-[#0D9488] rounded-full flex-shrink-0 mt-1 ml-2"></div>' : ''}
                                </div>
                                ${actions}
                            </div>
                        `;
                    }).join('');

                    document.querySelectorAll('.notif-accept-btn').forEach(btn => {
                        btn.addEventListener('click', () => respondToAccess(btn.dataset.accessId, 'accept', btn.dataset.notifId));
                    });
                    document.querySelectorAll('.notif-decline-btn').forEach(btn => {
                        btn.addEventListener('click', () => respondToAccess(btn.dataset.accessId, 'decline', btn.dataset.notifId));
                    });
                } else {
                    notificationList.innerHTML = `
                        <div class="p-4 text-center text-[#94a3b8]">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">Aucune notification</p>
                        </div>
                    `;
                }

                updateUnreadCount();
            } catch (error) {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Erreur du chargement</div>';
            }
        }

        // Update unread count badge
        async function updateUnreadCount() {
            try {
                const response = await fetch('{{ route('patient.unread-count') }}');
                const data = await response.json();
                unreadNotifCount.textContent = data.unread_count;
                if (data.unread_count > 0) {
                    notificationBadge.textContent = data.unread_count;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error updating unread count:', error);
            }
        }

        // Respond to access request (from notification panel)
        async function respondToAccess(accessId, action, notifId) {
            try {
                const response = await fetch('{{ route('patient.respond-access') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ access_id: accessId, action: action, notification_id: notifId }),
                });
                const data = await response.json();
                if (data.success) { showMessage(data.message, 'success'); loadNotifications(); }
                else { showMessage(data.message, 'error'); }
            } catch (error) {
                console.error('Error responding to access:', error);
                showMessage('Une erreur est survenue', 'error');
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // EXAM REQUESTS — Bubble Filters + Pagination
        // ─────────────────────────────────────────────────────────────────
        let allExamRequests = [];
        let activeFilter    = 'all';
        let currentPage     = 1;
        const PAGE_SIZE     = 5;

        const statusColors = {
            'pending':    'bg-amber-50 border-amber-200',
            'assigned':   'bg-teal-50 border-teal-200',
            'collected':  'bg-blue-50 border-blue-200',
            'processing': 'bg-purple-50 border-purple-200',
            'completed':  'bg-green-50 border-green-200',
            'cancelled':  'bg-red-50 border-red-200',
        };

        const statusLabels = {
            'pending':    'En attente',
            'assigned':   'Laboratoire sélectionné',
            'collected':  'Collectée',
            'processing': 'En traitement',
            'completed':  'Complétée',
            'cancelled':  'Annulée',
        };

        const statusBadgeColors = {
            'pending':    'text-amber-700 bg-amber-100 border-amber-300',
            'assigned':   'text-teal-700 bg-teal-100 border-teal-300',
            'collected':  'text-blue-700 bg-blue-100 border-blue-300',
            'processing': 'text-purple-700 bg-purple-100 border-purple-300',
            'completed':  'text-green-700 bg-green-100 border-green-300',
            'cancelled':  'text-red-700 bg-red-100 border-red-300',
        };

        const filterDefs = [
            { key: 'all',        label: 'Toutes',           base: 'text-[#0D9488] bg-[#0D9488]/10 border-[#0D9488]/30', active: 'bg-[#0D9488] text-white border-[#0D9488]' },
            { key: 'pending',    label: 'En attente',       base: 'text-amber-600 bg-amber-50 border-amber-200',          active: 'bg-amber-500 text-white border-amber-500' },
            { key: 'assigned',   label: 'Labo sélectionné', base: 'text-teal-600 bg-teal-50 border-teal-200',            active: 'bg-teal-600 text-white border-teal-600' },
            { key: 'collected',  label: 'Collectée',        base: 'text-blue-600 bg-blue-50 border-blue-200',            active: 'bg-blue-500 text-white border-blue-500' },
            { key: 'processing', label: 'En traitement',    base: 'text-purple-600 bg-purple-50 border-purple-200',       active: 'bg-purple-500 text-white border-purple-500' },
            { key: 'completed',  label: 'Complétée',        base: 'text-green-600 bg-green-50 border-green-200',         active: 'bg-green-500 text-white border-green-500' },
            { key: 'cancelled',  label: 'Annulée',          base: 'text-red-600 bg-red-50 border-red-200',               active: 'bg-red-500 text-white border-red-500' },
        ];

        async function loadExamRequests() {
            try {
                const response = await fetch('{{ route('patient.get-exam-requests') }}');
                const data     = await response.json();
                if (data.success) {
                    allExamRequests = data.exam_requests;
                    currentPage     = 1;
                    renderExamRequests();
                } else {
                    showEmptyExams();
                }
            } catch (error) {
                console.error('Error loading exam requests:', error);
                examRequestsList.innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Erreur du chargement</div>';
            }
        }

        function getFiltered() {
            return activeFilter === 'all'
                ? allExamRequests
                : allExamRequests.filter(r => r.status === activeFilter);
        }

        function renderFilterBubbles() {
            const counts = { all: allExamRequests.length };
            filterDefs.slice(1).forEach(f => { counts[f.key] = allExamRequests.filter(r => r.status === f.key).length; });
            const visible = filterDefs.filter(f => f.key === 'all' || counts[f.key] > 0);
            return `<div class="flex flex-wrap gap-2 mb-5" id="examFilterBubbles">
                ${visible.map(f => {
                    const isActive = activeFilter === f.key;
                    return `<button type="button" data-filter="${f.key}"
                        class="exam-filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border transition-all duration-200 select-none cursor-pointer
                               ${isActive ? f.active + ' shadow-sm scale-105' : f.base + ' hover:scale-105'}">
                        ${f.label}
                        <span class="inline-flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-1 rounded-full text-[10px] font-black
                                     ${isActive ? 'bg-white/25 text-current' : 'bg-black/10'}">
                            ${counts[f.key]}
                        </span>
                    </button>`;
                }).join('')}
            </div>`;
        }

        /* ── Status stepper helper (Task 3.8) ─────────────────────────────── */
        const STATUS_STEPS = [
            { key: 'pending',    icon: '📋', label: 'Prescription' },
            { key: 'assigned',   icon: '🏥', label: 'Labo choisi'  },
            { key: 'collected',  icon: '🩸', label: 'Collectée'    },
            { key: 'processing', icon: '🔬', label: 'En traitement'},
            { key: 'completed',  icon: '✅', label: 'Résultats'    },
        ];

        function renderStatusStepper(status) {
            if (status === 'cancelled') {
                return `<div class="my-3 flex items-center gap-2 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-xl px-3 py-2">
                    <span>❌</span><span>Demande annulée</span>
                </div>`;
            }
            const currentIdx = STATUS_STEPS.findIndex(s => s.key === status);
            const dots = STATUS_STEPS.map((step, idx) => {
                const done    = idx <= currentIdx;
                const current = idx === currentIdx;
                const dotCls  = done
                    ? 'bg-[#0D9488] border-[#0D9488] text-white shadow-sm shadow-[#0D9488]/30'
                    : 'bg-white border-[#cbd5e1] text-[#94a3b8]';
                const labelCls = current
                    ? 'text-[#0D9488] font-extrabold'
                    : done
                        ? 'text-[#475569] font-semibold'
                        : 'text-[#94a3b8]';
                const lineCls  = idx < STATUS_STEPS.length - 1
                    ? (idx < currentIdx ? 'bg-[#0D9488]' : 'bg-[#e2e8f0]')
                    : 'hidden';
                return `
                    <div class="flex flex-col items-center relative" style="min-width:0;flex:1">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-[10px] ${dotCls} transition-all duration-300">
                            ${done ? (current ? step.icon : '✓') : ''}
                        </div>
                        <span class="text-[9px] mt-1 text-center leading-tight ${labelCls}">${step.label}</span>
                        ${idx < STATUS_STEPS.length - 1
                            ? `<div class="absolute top-3 left-[calc(50%+12px)] right-[calc(-50%+12px)] h-0.5 ${lineCls} -translate-y-1/2 transition-all duration-300" style="z-index:0"></div>`
                            : ''}
                    </div>`;
            }).join('');

            return `<div class="flex items-start mt-3 mb-1 px-1 relative">${dots}</div>`;
        }

        function renderExamCard(request) {
            const cardColor  = statusColors[request.status]      || 'bg-[#F8FAFC]/50 border-[#e2e8f0]/60';
            const badgeColor = statusBadgeColors[request.status] || 'text-[#0D9488] bg-[#0D9488]/10 border-[#0D9488]/20';
            const label      = statusLabels[request.status]      || request.status;

            let labSection = '';
            if (request.laboratory) {
                labSection = `
                    <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl text-sm">
                        <p class="text-green-800 font-medium mb-2">
                            🏥 <strong>${request.laboratory.name}</strong>
                            ${request.laboratory.city ? `<span class="text-green-600 font-normal ml-1">— ${request.laboratory.city}</span>` : ''}
                        </p>
                        ${(request.status === 'pending' || request.status === 'assigned') ? `
                        <a href="/patient/exam-requests/${request.id}/choose-laboratory"
                            class="flex items-center justify-center gap-1.5 w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded-lg transition uppercase tracking-wider text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Changer le laboratoire
                        </a>` : ''}
                    </div>`;
            } else if (request.status === 'pending' || request.status === 'assigned') {
                labSection = `
                    <a href="/patient/exam-requests/${request.id}/choose-laboratory"
                        class="flex items-center justify-center gap-1.5 w-full mt-3 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Choisir un laboratoire
                    </a>`;
            }

            const detailBtn = request.approved_by_doctor
                ? `<button type="button" class="viewExamsBtn mt-4 w-full bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs" data-exam-request-id="${request.id}">Voir les Détails</button>`
                : `<button type="button" class="mt-4 w-full bg-gray-300 text-gray-500 font-bold py-2.5 rounded-xl uppercase tracking-wider text-xs cursor-not-allowed" disabled>En attente d'approbation</button>`;

            return `
                <div class="${cardColor} border rounded-2xl p-5 transition-all duration-200 hover:shadow-md">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-bold text-[#1e293b]">Dr. ${request.doctor_name}</h4>
                            <p class="text-sm text-[#64748b]">${request.doctor_speciality}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-bold border rounded-full uppercase whitespace-nowrap ${badgeColor}">
                            ${label}
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-[#64748b] mb-1">
                        <span><strong class="text-[#1e293b]">${request.exams_count}</strong> examen(s)</span>
                        <span class="text-[#94a3b8]">${request.created_at_relative}</span>
                    </div>
                    ${renderStatusStepper(request.status)}
                    ${labSection}
                    ${detailBtn}
                </div>`;
        }

        function renderPagination(filtered) {
            const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
            if (totalPages <= 1) return '';
            const start = (currentPage - 1) * PAGE_SIZE + 1;
            const end   = Math.min(currentPage * PAGE_SIZE, filtered.length);
            const pages = [];
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) pages.push(i);
            const pageButtons = pages.map(p =>
                `<button type="button" data-page="${p}" class="exam-page-btn w-8 h-8 rounded-full text-xs font-bold transition-all duration-200
                       ${p === currentPage ? 'bg-[#0D9488] text-white shadow-sm' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">${p}</button>`
            ).join('');
            return `
                <div class="flex items-center justify-between mt-5 pt-4 border-t border-[#e2e8f0]/60">
                    <p class="text-xs text-[#94a3b8] font-medium">${start}–${end} sur ${filtered.length}</p>
                    <div class="flex items-center gap-1">
                        <button type="button" id="examPrevBtn" ${currentPage === 1 ? 'disabled' : ''}
                            class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200
                                   ${currentPage === 1 ? 'text-[#cbd5e1] cursor-not-allowed' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        ${pageButtons}
                        <button type="button" id="examNextBtn" ${currentPage === totalPages ? 'disabled' : ''}
                            class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200
                                   ${currentPage === totalPages ? 'text-[#cbd5e1] cursor-not-allowed' : 'text-[#64748b] hover:bg-[#0D9488]/10 hover:text-[#0D9488]'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>`;
        }

        function renderExamRequests() {
            const filtered   = getFiltered();
            const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
            if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
            const paginated  = filtered.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

            if (allExamRequests.length === 0) { showEmptyExams(); return; }

            let html = renderFilterBubbles();

            if (filtered.length === 0) {
                html += `<div class="p-8 text-center text-[#94a3b8]">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-semibold">Aucune demande pour ce filtre</p>
                    <p class="text-xs mt-1 opacity-70">Essayez un autre statut</p>
                </div>`;
            } else {
                html += `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">${paginated.map(renderExamCard).join('')}</div>`;
                html += renderPagination(filtered);
            }

            examRequestsList.innerHTML = html;

            document.querySelectorAll('.exam-filter-btn').forEach(btn => {
                btn.addEventListener('click', () => { activeFilter = btn.dataset.filter; currentPage = 1; renderExamRequests(); });
            });
            document.querySelectorAll('.exam-page-btn').forEach(btn => {
                btn.addEventListener('click', () => { currentPage = parseInt(btn.dataset.page); renderExamRequests(); examRequestsList.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
            });
            const prevBtn = document.getElementById('examPrevBtn');
            const nextBtn = document.getElementById('examNextBtn');
            if (prevBtn && currentPage > 1) prevBtn.addEventListener('click', () => { currentPage--; renderExamRequests(); examRequestsList.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
            if (nextBtn && currentPage < Math.ceil(filtered.length / PAGE_SIZE)) nextBtn.addEventListener('click', () => { currentPage++; renderExamRequests(); examRequestsList.scrollIntoView({ behavior: 'smooth', block: 'start' }); });
            document.querySelectorAll('.viewExamsBtn').forEach(btn => {
                btn.addEventListener('click', () => viewExamDetails(btn.dataset.examRequestId));
            });
        }

        function showEmptyExams() {
            examRequestsList.innerHTML = `
                <div class="p-8 text-center text-[#94a3b8]">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm font-semibold">Aucune demande d'analyse</p>
                    <p class="text-xs mt-1">Les demandes d'examens prescrits par vos médecins apparaîtront ici.</p>
                </div>`;
        }

        // Load access requests from doctors
        async function loadAccessRequests() {
            try {
                const response = await fetch('{{ route('patient.get-access-requests') }}');
                const data = await response.json();

                const accessRequestsList = document.getElementById('accessRequestsList');

                if (data.success && data.access_requests.length > 0) {
                    accessRequestsList.innerHTML = data.access_requests.map(request => `
                        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#0D9488] to-[#0a7068] flex items-center justify-center text-white font-bold">
                                            ${request.doctor_name.split(' ').map(n => n[0]).join('').substring(0, 2)}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-[#1e293b]">Dr. ${request.doctor_name}</h4>
                                            <p class="text-sm text-[#0D9488]">${request.doctor_speciality}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-[#64748b] mt-2">
                                        <strong>Demande reçue:</strong> ${request.created_at_relative}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-xs font-bold text-amber-700 bg-amber-100 border border-amber-200 rounded-full uppercase">
                                    EN ATTENTE
                                </span>
                            </div>

                            <div class="flex gap-3 pt-4 border-t border-blue-200">
                                <button type="button" class="flex-1 bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs access-accept-btn" data-access-id="${request.id}">
                                    ✓ Accepter
                                </button>
                                <button type="button" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs access-decline-btn" data-access-id="${request.id}">
                                    ✗ Refuser
                                </button>
                            </div>
                        </div>
                    `).join('');

                    // Add event listeners for access requests panel
                    document.querySelectorAll('.access-accept-btn').forEach(btn => {
                        btn.addEventListener('click', () => respondToAccessRequest(btn.dataset.accessId,
                            'accepted'));
                    });
                    document.querySelectorAll('.access-decline-btn').forEach(btn => {
                        btn.addEventListener('click', () => respondToAccessRequest(btn.dataset.accessId,
                            'declined'));
                    });
                } else {
                    accessRequestsList.innerHTML = `
                        <div class="p-8 text-center text-[#94a3b8]">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-semibold">Aucune demande en attente</p>
                            <p class="text-xs mt-1">Les demandes d'accès des médecins apparaîtront ici.</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading access requests:', error);
                document.getElementById('accessRequestsList').innerHTML =
                    '<div class="p-4 text-center text-red-500 text-sm">Erreur du chargement</div>';
            }
        }

        // Respond to access request
        async function respondToAccessRequest(accessId, action) {
            try {
                const response = await fetch('{{ route('patient.respond-access') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        access_id: accessId,
                        action: action,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(`Demande d'accès ${action === 'accepted' ? 'acceptée ✓' : 'refusée ✗'}`, 'success');
                    loadAccessRequests();
                    loadNotifications();
                    updateUnreadCount();
                } else {
                    showMessage(data.message || 'Erreur lors du traitement', 'error');
                }
            } catch (error) {
                console.error('Error responding to access:', error);
                showMessage('Une erreur est survenue', 'error');
            }
        }

        // View exam details
        async function viewExamDetails(examRequestId) {
            try {
                const response = await fetch(`/patient/exam-requests/${examRequestId}`);
                const data = await response.json();

                if (data.success) {
                    const exam = data.exam_request;

                    
                    document.getElementById('modalDoctorName').textContent = `Dr. ${exam.doctor_name}`;
                    document.getElementById('modalDoctorSpeciality').textContent = exam.doctor_speciality;
                    document.getElementById('modalDoctorPhone').textContent = exam.doctor_phone || 'Non disponible';
                    document.getElementById('modalStatus').textContent = exam.status;

                    if (exam.approved_by_doctor) {
                        document.getElementById('modalDoctorInterpretationSection').classList.remove('hidden');
                        document.getElementById('modalDoctorInterpretation').textContent = exam.doctor_interpretation || 'Aucune interprétation.';
                    } else {
                        document.getElementById('modalDoctorInterpretationSection').classList.add('hidden');
                    }


                    if (exam.clinical_notes) {
                        document.getElementById('clinicalNotesSection').classList.remove('hidden');
                        document.getElementById('modalClinicalNotes').textContent = exam.clinical_notes;
                    } else {
                        document.getElementById('clinicalNotesSection').classList.add('hidden');
                    }

                    const examsList = document.getElementById('modalExamsList');
                    examsList.innerHTML = exam.exams.map(exam => `
                        <div class="p-3 bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#0D9488] flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <h5 class="font-bold text-sm text-[#1e293b]">${exam.name}</h5>
                                    ${exam.category ? `<p class="text-xs text-[#0D9488] mt-0.5">${exam.category}</p>` : ''}
                                    ${exam.description ? `<p class="text-xs text-[#64748b] mt-1">${exam.description}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Set up print button redirection
                    document.getElementById('modalPrintBtn').onclick = () => {
                        window.location.href = `/patient/exam-requests/${examRequestId}/print?auto=1`;
                    };

                    examDetailsModal.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error loading exam details:', error);
                showMessage('Erreur du chargement des détails', 'error');
            }
        }

        // Close modal
        document.querySelectorAll('.closeExamModal').forEach(btn => {
            btn.addEventListener('click', () => {
                examDetailsModal.classList.add('hidden');
            });
        });

        // Show message helper
        function showMessage(message, type) {
            const alert = document.createElement('div');
            alert.className =
                `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white p-4 rounded-xl shadow-lg z-50 flex items-center gap-2`;
            alert.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>${message}</span>
            `;
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 2000);
        }

        // Load data on page load
        loadNotifications();
        updateUnreadCount();
        loadExamRequests();
        loadAccessRequests();
        loadGrantedDoctors();
        loadBlockedDoctors();

        // Refresh notifications every 10 seconds
        setInterval(updateUnreadCount, 10000);
        // Refresh access requests every 15 seconds
        setInterval(loadAccessRequests, 15000);

        // ── Block / Unblock Doctor ──
        async function loadGrantedDoctors() {
            const list = document.getElementById('grantedDoctorsList');
            try {
                const res = await fetch('{{ route("patient.get-granted-doctors") }}');
                const data = await res.json();
                if (data.success && data.granted.length > 0) {
                    list.innerHTML = data.granted.map(doc => `
                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs">
                                    ${doc.doctor_name.split(' ').map(n => n[0]).join('').substring(0,2)}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-[#1e293b]">Dr. ${doc.doctor_name}</p>
                                    <p class="text-[11px] text-[#64748b]">${doc.speciality || ''}</p>
                                    ${doc.expires_at ? `<p class="text-[10px] text-[#94a3b8]">Expire le ${doc.expires_at}</p>` : ''}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="/patient/chat/${doc.doctor_id}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-[#0D9488] text-white hover:bg-[#0a7068] transition" title="Envoyer un message">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Chat
                                </a>
                                <button onclick="blockDoctor(${doc.doctor_id})" class="text-[10px] font-bold text-red-500 hover:underline cursor-pointer uppercase">Bloquer</button>
                            </div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<p class="text-xs text-[#94a3b8] text-center py-2">Aucun médecin autorisé</p>';
                }
            } catch (e) {
                list.innerHTML = '<p class="text-xs text-[#94a3b8] text-center py-2">Erreur de chargement</p>';
            }
        }

        async function blockDoctor(doctorId) {
            const result = await Swal.fire({
                title: 'Bloquer ce médecin ?',
                text: "Il ne pourra plus voir votre profil ni accéder à votre dossier.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Oui, bloquer',
                cancelButtonText: 'Annuler'
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch('{{ route("patient.block-doctor") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ doctor_id: doctorId }),
                });
                const data = await res.json();
                if (data.success) {
                    showMessage(data.message, 'success');
                    loadBlockedDoctors();
                    loadAccessRequests();
                } else {
                    showMessage(data.message || 'Erreur', 'error');
                }
            } catch (e) { showMessage('Une erreur est survenue', 'error'); }
        }

        async function unblockDoctor(doctorId) {
            const result = await Swal.fire({
                title: 'Débloquer ce médecin ?',
                text: "Le médecin pourra à nouveau accéder à votre profil.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Oui, débloquer',
                cancelButtonText: 'Annuler'
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch('{{ route("patient.unblock-doctor") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ doctor_id: doctorId }),
                });
                const data = await res.json();
                if (data.success) {
                    showMessage(data.message, 'success');
                    loadBlockedDoctors();
                } else {
                    showMessage(data.message || 'Erreur', 'error');
                }
            } catch (e) { showMessage('Une erreur est survenue', 'error'); }
        }

        async function loadBlockedDoctors() {
            const list = document.getElementById('blockedList');
            try {
                const res = await fetch('{{ route("patient.get-blocked-doctors") }}');
                const data = await res.json();
                if (data.success && data.blocked.length > 0) {
                    list.innerHTML = data.blocked.map(doc => `
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-xs">
                                    ${doc.doctor_name.split(' ').map(n => n[0]).join('').substring(0,2)}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-[#1e293b]">Dr. ${doc.doctor_name}</p>
                                    <p class="text-[11px] text-[#64748b]">${doc.speciality || ''}</p>
                                </div>
                            </div>
                            <button onclick="unblockDoctor(${doc.doctor_id})" class="text-[10px] font-bold text-[#0D9488] hover:underline cursor-pointer uppercase">Débloquer</button>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<p class="text-xs text-[#94a3b8] text-center py-2">Aucun médecin bloqué</p>';
                }
            } catch (e) {
                list.innerHTML = '<p class="text-xs text-[#94a3b8] text-center py-2">Erreur de chargement</p>';
            }
        }
    </script>
    @endsection
</x-layouts.patient>

<x-layouts.auth>
    <x-slot:title>Tableau de bord Patient - Medix eSanté</x-slot:title>

    <div class="w-full max-w-[800px] mx-auto py-8">
        <div class="glass-card rounded-[20px] p-8 md:p-10 relative overflow-hidden">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-[#e2e8f0]/80 pb-6 mb-8 select-none">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <div class="w-12 h-12 rounded-full bg-white border-2 border-[#0D9488] flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-[#0D9488]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20V8l8 7 8-7v12" />
                            <path d="M12 3v4M10 5h4" stroke-width="2.2" stroke="#0D9488" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-[#1e293b]">Espace Patient</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wider text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 uppercase mt-1">
                            PATIENT
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notificationBell" type="button" class="relative p-2 text-[#64748b] hover:text-[#0D9488] transition" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notificationBadge" class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">0</span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-[#e2e8f0] z-50 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#0D9488] to-[#0a7068] p-4 text-white">
                                <h3 class="font-bold">Notifications</h3>
                                <p class="text-xs text-[#0D9488]/80">Vous avez <span id="unreadNotifCount">0</span> notification(s)</p>
                            </div>

                            <div id="notificationList" class="max-h-96 overflow-y-auto">
                                <!-- Notifications loaded here -->
                                <div class="p-4 text-center text-[#94a3b8]">
                                    <p class="text-sm">Chargement des notifications...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('patient.logout') }}" method="POST">
                        @csrf
                        <x-button type="submit" color="slate" :fullWidth="false" class="!py-1.5 !px-4 !text-xs">
                            SE DÉCONNECTER
                        </x-button>
                    </form>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Greeting -->
                <div>
                    <h1 class="text-2xl font-bold text-[#1e293b]">
                        Bonjour, <span class="text-[#0D9488]">{{ $user->first_name }} {{ $user->last_name }}</span> !
                    </h1>
                    <p class="text-sm text-[#64748b] mt-1 font-medium leading-relaxed">
                        Bienvenue sur votre espace patient sécurisé. Vous pouvez consulter vos demandes d'examens et informations personnelles.
                    </p>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <!-- Profile Info -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Informations Personnelles
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Code Unique Patient :</span>
                                <span class="font-bold text-[#1e293b]">{{ $user->patient->patient_code }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Adresse Email :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-[#e2e8f0]/40">
                                <span class="text-[#64748b] font-medium">Téléphone :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->phone }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-[#64748b] font-medium">Adresse :</span>
                                <span class="font-semibold text-[#1e293b]">{{ $user->address ?? 'Non renseignée' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Activity -->
                    <div class="bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-2xl p-6">
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider mb-4 flex items-center">
                            <svg class="w-4 h-4 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Activité Médicale
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#0D9488]/10 flex items-center justify-center text-[#0D9488] font-bold text-sm">
                                        {{ $user->patient->examRequests()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Demandes d'analyses</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#0D9488]/10 flex items-center justify-center text-[#0D9488] font-bold text-sm">
                                        {{ $user->patient->doctorAccesses()->count() }}
                                    </div>
                                    <span class="text-xs font-semibold text-[#64748b]">Médecins autorisés</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Access Requests Section -->
                <div class="border-t border-[#e2e8f0] pt-8">
                    <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                        <svg class="w-5 h-5 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 10H9m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Demandes d'Accès en Attente
                    </h3>
                    <div id="accessRequestsList" class="space-y-3">
                        <!-- Access requests loaded here -->
                        <div class="p-4 text-center text-[#94a3b8]">
                            <p class="text-sm">Chargement des demandes...</p>
                        </div>
                    </div>
                </div>

                <!-- Section link details -->
                <div class="bg-teal-50/40 border border-[#0D9488]/10 rounded-2xl p-6 select-none mt-8">
                    <h4 class="text-xs font-bold text-[#0D9488] uppercase tracking-wider mb-2">Note d'intégration</h4>
                    <p class="text-xs text-[#64748b] leading-relaxed">
                        Ce tableau de bord est entièrement dynamique et connecté aux modèles Eloquent de la base de données (<span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\Patient</span>, <span class="font-mono bg-white px-1 py-0.5 rounded border">App\Models\User</span>). Vos informations d'inscription ont été correctement insérées.
                    </p>
                </div>

                <!-- Exam Requests Section -->
                <div class="pt-4">
                    <h3 class="text-lg font-bold text-[#1e293b] mb-4 flex items-center">
                        <svg class="w-5 h-5 text-[#0D9488] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Vos Demandes d'Analyses
                    </h3>
                    <div id="examRequestsList" class="grid grid-cols-1 gap-4">
                        <!-- Exam requests loaded here -->
                        <div class="p-4 text-center text-[#94a3b8]">
                            <p class="text-sm">Chargement des demandes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details Modal -->
    <div id="examDetailsModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-[20px] max-w-[600px] w-full max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#e2e8f0]/80">
                    <h3 class="text-lg font-bold text-[#1e293b]">Détails de la Demande</h3>
                    <button type="button" class="closeExamModal text-[#94a3b8] hover:text-[#1e293b]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Doctor Info -->
                <div class="mb-6 p-4 bg-[#0D9488]/10 border border-[#0D9488]/20 rounded-xl">
                    <p class="text-xs font-bold text-[#0D9488] uppercase tracking-wider mb-2">Prescrit par</p>
                    <p id="modalDoctorName" class="text-lg font-bold text-[#1e293b]"></p>
                    <p id="modalDoctorSpeciality" class="text-sm text-[#64748b]"></p>
                    <p id="modalDoctorPhone" class="text-sm text-[#64748b] mt-2"><strong>Tel:</strong> <span id="phoneValue"></span></p>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-2">Statut</p>
                    <span id="modalStatus" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 uppercase"></span>
                </div>

                <!-- Clinical Notes -->
                <div id="clinicalNotesSection" class="mb-6 hidden">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-2">Notes Cliniques</p>
                    <div id="modalClinicalNotes" class="p-3 bg-[#F8FAFC]/50 border border-[#e2e8f0]/60 rounded-xl text-sm text-[#64748b]"></div>
                </div>

                <!-- Exams List -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-3">Examens Prescrits</p>
                    <div id="modalExamsList" class="space-y-3">
                        <!-- Exams will be loaded here -->
                    </div>
                </div>

                <!-- Close Button -->
                <button type="button" class="closeExamModal w-full bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-sm">
                    Fermer
                </button>
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

        // Load and display notifications
        async function loadNotifications() {
            try {
                const response = await fetch('{{ route('patient.get-notifications') }}');
                const data = await response.json();

                if (data.success && data.notifications.length > 0) {
                    notificationList.innerHTML = data.notifications.map(notif => {
                        const isAccessRequest = notif.type === 'access_request';
                        const isExamRequest = notif.type === 'exam_request';

                        let actions = '';
                        if (isAccessRequest) {
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

                    // Add event listeners for accept/decline buttons in notification panel
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

                // Update unread count
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

        // Respond to access request
        async function respondToAccess(accessId, action, notifId) {
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
                        notification_id: notifId,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    loadNotifications();
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error responding to access:', error);
                showMessage('Une erreur est survenue', 'error');
            }
        }

        // Load exam requests
        async function loadExamRequests() {
            try {
                const response = await fetch('{{ route('patient.get-exam-requests') }}');
                const data = await response.json();

                if (data.success && data.exam_requests.length > 0) {
                    examRequestsList.innerHTML = data.exam_requests.map(request => {
                        const statusColors = {
                            'pending': 'bg-amber-50 border-amber-200',
                            'collected': 'bg-blue-50 border-blue-200',
                            'processing': 'bg-purple-50 border-purple-200',
                            'completed': 'bg-green-50 border-green-200',
                            'cancelled': 'bg-red-50 border-red-200',
                        };

                        const statusLabels = {
                            'pending': 'En attente',
                            'collected': 'Collectée',
                            'processing': 'En traitement',
                            'completed': 'Complétée',
                            'cancelled': 'Annulée',
                        };

                        return `
                            <div class="${statusColors[request.status] || 'bg-[#F8FAFC]/50 border-[#e2e8f0]/60'} border rounded-2xl p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-[#1e293b]">Dr. ${request.doctor_name}</h4>
                                        <p class="text-sm text-[#64748b]">${request.doctor_speciality}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-bold text-[#0D9488] bg-[#0D9488]/10 border border-[#0D9488]/20 rounded-full uppercase">
                                        ${statusLabels[request.status]}
                                    </span>
                                </div>
                                <p class="text-xs text-[#64748b] mb-3">
                                    <strong>${request.exams_count}</strong> examen(s) prescrit(s)
                                </p>
                                <p class="text-xs text-[#94a3b8] mb-4">${request.created_at_relative}</p>
                                <button type="button" class="w-full bg-[#0D9488] hover:bg-[#0a7068] text-white font-bold py-2.5 rounded-xl transition uppercase tracking-wider text-xs viewExamsBtn" data-exam-request-id="${request.id}">
                                    Voir les Détails
                                </button>
                            </div>
                        `;
                    }).join('');

                    // Add event listeners for view exams buttons
                    document.querySelectorAll('.viewExamsBtn').forEach(btn => {
                        btn.addEventListener('click', () => viewExamDetails(btn.dataset.examRequestId));
                    });
                } else {
                    examRequestsList.innerHTML = `
                        <div class="p-8 text-center text-[#94a3b8]">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-semibold">Aucune demande d'analyse</p>
                            <p class="text-xs mt-1">Les demandes d'examens prescrits par vos médecins apparaîtront ici.</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading exam requests:', error);
                examRequestsList.innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Erreur du chargement</div>';
            }
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
                        btn.addEventListener('click', () => respondToAccessRequest(btn.dataset.accessId, 'accepted'));
                    });
                    document.querySelectorAll('.access-decline-btn').forEach(btn => {
                        btn.addEventListener('click', () => respondToAccessRequest(btn.dataset.accessId, 'declined'));
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
                document.getElementById('accessRequestsList').innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Erreur du chargement</div>';
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
            alert.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white p-4 rounded-xl shadow-lg z-50 flex items-center gap-2`;
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

        // Refresh notifications every 10 seconds
        setInterval(updateUnreadCount, 10000);
        // Refresh access requests every 15 seconds
        setInterval(loadAccessRequests, 15000);
    </script>
</x-layouts.auth>

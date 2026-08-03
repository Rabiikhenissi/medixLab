<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LaboResultController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MachineConfigurationController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UserController;
use App\Models\DoctorPatientAccess;
use App\Models\ExamRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Landing Selection Portal
// Root redirects to the role-aware home page
Route::get('/', function () {
    return redirect()->route('home');
});

// Home portal: bounce each logged-in role to its own dashboard, else show the landing page
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->admin) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->doctor) {
            return redirect()->route('doctor.dashboard');
        }
        if ($user->patient) {
            return redirect()->route('patient.dashboard');
        }
        if ($user->staff) {
            return redirect()->route('center.dashboard');
        }
    }

    return view('portal');
})->name('home');

// Doctor routes
Route::prefix('doctor')->name('doctor.')->group(function () {

    // QR Code scan - role-aware (public, so old/wrong QRs never strand users)
    // Public entry point: route the scanned code to the right role's screen
    Route::get('/scan/{code}', function ($code) {
        $user = auth()->user();
        if ($user && $user->doctor) {
            return redirect()->route('doctor.dashboard', ['scan' => $code]);
        }
        if ($user && $user->patient) {
            return redirect()->route('patient.scan-doctor', ['code' => $code]);
        }

        return redirect()->route('home');
    })->name('scan-patient');

    // Guest routes
    Route::middleware('guest')->group(function () {

        Route::get('/login', fn () => view('doctor.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'doctor');

        Route::get('/register', fn () => view('doctor.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:register')
            ->defaults('role', 'doctor');

        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'doctor')
            ->middleware('throttle:password-reset')
            ->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'doctor')
            ->middleware('throttle:password-reset')
            ->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'doctor')
            ->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'doctor')
            ->middleware('throttle:password-reset')
            ->name('password.update');
    });

    // Authenticated doctor routes
    Route::middleware(['auth', 'doctor', 'throttle:general'])->group(function () {
        // Doctor dashboard: recent patients, filtered exam requests and cached analytics
        Route::get('/dashboard', function () {
            $user = auth()->user();
            if (! $user->doctor) {
                return redirect()->route('home');
            }
            $doctor = $user->doctor;

            $recentPatients = DoctorPatientAccess::where('doctor_id', $doctor->id)
                ->where('access_status', 'granted')
                ->with('patient.user')
                ->latest('updated_at')
                ->limit(20)
                ->get();

            $search = request('search');
            $status = request('status');

            $query = ExamRequest::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'items.exam', 'items.resultLabo.details']);

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('patient.user', function ($qp) use ($search) {
                        $qp->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })->orWhereHas('patient', function ($qp) use ($search) {
                        $qp->where('patient_code', 'like', "%{$search}%");
                    });
                });
            }

            $recentExams = $query->latest('created_at')
                ->paginate(10)
                ->withQueryString();

            // Cache sidebar stats + quick exam groups (10 min)
            $doctorStats = cache()->remember("doctor_stats_{$doctor->id}", 600, function () use ($doctor) {
                return [
                    'examGroupsCount' => $doctor->examGroups()->count(),
                    'examRequestsCount' => $doctor->examRequests()->count(),
                ];
            });

            $doctorGroups = cache()->remember("doctor_groups_{$doctor->id}", 600, function () use ($doctor) {
                return $doctor->examGroups()->where('is_archive', false)->with('items.exam')->get();
            });

            // Cache analytics for 10 minutes
            $cacheKey = "doctor_analytics_{$doctor->id}";
            $analytics = cache()->remember($cacheKey, 600, function () use ($doctor) {
                $monthExpr = DB::connection()->getDriverName() === 'sqlite'
                    ? "strftime('%Y-%m', created_at)"
                    : "DATE_FORMAT(created_at, '%Y-%m')";

                $monthlyPrescriptions = ExamRequest::where('doctor_id', $doctor->id)
                    ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                    ->selectRaw("{$monthExpr} as month_key, COUNT(*) as count")
                    ->groupBy('month_key')
                    ->orderBy('month_key')
                    ->pluck('count', 'month_key');

                $allMonths = collect();
                for ($i = 5; $i >= 0; $i--) {
                    $d = now()->subMonths($i);
                    $allMonths->push([
                        'label' => $d->format('M Y'),
                        'count' => $monthlyPrescriptions[$d->format('Y-m')] ?? 0,
                    ]);
                }
                $chartData = $allMonths->values();

                $uniquePatientsCount = ExamRequest::where('doctor_id', $doctor->id)
                    ->distinct('patient_id')
                    ->count('patient_id');

                $totalRequests = ExamRequest::where('doctor_id', $doctor->id)->count();
                $completedRequests = ExamRequest::where('doctor_id', $doctor->id)
                    ->where('status', 'completed')
                    ->count();
                $completionRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) : 0;

                return compact('chartData', 'uniquePatientsCount', 'completionRate');
            });

            return view('doctor.dashboard', array_merge(
                compact('user', 'recentPatients', 'recentExams', 'search', 'status', 'doctorStats', 'doctorGroups'),
                $analytics
            ));
        })->name('dashboard');

        // Doctor Interface Routes
        Route::get('/patient-search', [DoctorController::class, 'patientSearch'])->name('patient-search');
        Route::post('/search-patient', [DoctorController::class, 'searchPatient'])->name('search-patient');
        Route::post('/request-access', [DoctorController::class, 'requestAccess'])->name('request-access');
        Route::get('/exams-selection/{patient}', [DoctorController::class, 'selectExams'])->name('select-exams');
        Route::post('/create-exam-request', [DoctorController::class, 'createExamRequest'])->name('create-exam-request');
        Route::get('/my-patients', [DoctorController::class, 'myPatients'])->name('my-patients');
        Route::post('/exam-requests/{examRequest}/submit-interpretation', [DoctorController::class, 'submitInterpretation'])->name('submit-interpretation');
        Route::post('/apply-exam-group', [DoctorController::class, 'applyExamGroup'])->name('apply-exam-group');
        Route::post('/api/exam-groups', [DoctorController::class, 'storeExamGroupApi'])->name('api.store-exam-group');

        // Exam Groups CRUD (dedicated pages)
        Route::get('/exam-groups', [DoctorController::class, 'examGroupsIndex'])->name('exam-groups.index');
        Route::get('/exam-groups/create', [DoctorController::class, 'examGroupsCreate'])->name('exam-groups.create');
        Route::post('/exam-groups', [DoctorController::class, 'examGroupsStore'])->name('exam-groups.store');
        Route::get('/exam-groups/{examGroup}/edit', [DoctorController::class, 'examGroupsEdit'])->name('exam-groups.edit');
        Route::put('/exam-groups/{examGroup}', [DoctorController::class, 'examGroupsUpdate'])->name('exam-groups.update');
        Route::delete('/exam-groups/{examGroup}', [DoctorController::class, 'examGroupsDestroy'])->name('exam-groups.destroy');

        // PDF / Print export for a completed exam request (Task 3.2)
        Route::get('/exam-requests/{examRequest}/print', [DoctorController::class, 'printExamRequest'])
            ->name('print-exam-request');

        // Cancel exam request (permission-less: cancelling is always allowed)
        Route::post('/exam-requests/{examRequest}/cancel', [DoctorController::class, 'cancelExamRequest'])
            ->name('cancel-exam-request');
        Route::post('/exam-requests/{examRequest}/cancel', [DoctorController::class, 'cancelExamRequest'])
            ->name('cancel-exam-request');

        // Notifications
        Route::get('/notifications', [DoctorController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [DoctorController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [DoctorController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [DoctorController::class, 'markAllAsRead'])->name('mark-all-read');

        // TIER 1.4 — Smart Exam Suggestions
        Route::get('/api/smart-suggestions/{patient}', [DoctorController::class, 'smartSuggestions'])->name('smart-suggestions');
        Route::get('/api/patient-health-trends/{patient}', [DoctorController::class, 'patientHealthTrends'])->name('patient-health-trends');

        // TIER 2.2 — Doctor-Patient Chat
        Route::get('/chat/{patient}', [DoctorController::class, 'chat'])->name('chat');
        Route::get('/chat/{patient}/messages', [DoctorController::class, 'chatMessages'])->name('chat-messages');
        Route::post('/chat/{patient}/send', [DoctorController::class, 'chatSend'])->name('chat-send');
        Route::get('/chat/unread-count', [DoctorController::class, 'chatUnreadCount'])->name('chat-unread-count');

        // Patient Medical Records
        Route::get('/patients/{patient}/medical-records', [DoctorController::class, 'medicalRecords'])->name('medical-records');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'doctor')->name('logout');
    });
});

// Patient routes
Route::prefix('patient')->name('patient.')->group(function () {

    // Guest routes
    Route::middleware('guest')->group(function () {

        Route::get('/login', fn () => view('patient.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'patient');

        Route::get('/register', fn () => view('patient.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:register')
            ->defaults('role', 'patient');

        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'patient')
            ->middleware('throttle:password-reset')
            ->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'patient')
            ->middleware('throttle:password-reset')
            ->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'patient')
            ->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'patient')
            ->middleware('throttle:password-reset')
            ->name('password.update');

    });

    // Authenticated patient routes
    Route::middleware(['auth', 'patient', 'throttle:general'])->group(function () {
        // Patient dashboard: cached sidebar stats only
        Route::get('/dashboard', function () {
            if (! auth()->user()->patient) {
                return redirect()->route('home');
            }
            $user = auth()->user();
            $patient = $user->patient;

            // Cache sidebar stats (10 min)
            $patientStats = cache()->remember("patient_stats_{$patient->id}", 600, function () use ($patient) {
                return [
                    'examRequestsCount' => $patient->examRequests()->count(),
                    'grantedDoctorsCount' => $patient->doctorAccesses()->where('access_status', 'granted')->count(),
                ];
            });

            return view('patient.dashboard', ['user' => $user, 'patientStats' => $patientStats]);
        })->name('dashboard');

        Route::post('/save-location', [PatientController::class, 'saveLocation'])
            ->name('save-location');

        Route::get('/analytics', function () {
            if (! auth()->user()->patient) {
                return redirect()->route('home');
            }
            $user = auth()->user();
            $patient = $user->patient;

            $statusCounts = $patient->examRequests()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $monthExpr = DB::connection()->getDriverName() === 'sqlite'
                ? "strftime('%Y-%m', created_at)"
                : "DATE_FORMAT(created_at, '%Y-%m')";

            $monthlyData = $patient->examRequests()
                ->where('created_at', '>=', now()->subMonths(6))
                ->selectRaw("{$monthExpr} as month, count(*) as count")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $chartData = collect();
            for ($i = 5; $i >= 0; $i--) {
                $key = now()->subMonths($i)->format('Y-m');
                $chartData->push([
                    'label' => now()->subMonths($i)->format('M'),
                    'count' => $monthlyData[$key] ?? 0,
                ]);
            }

            return view('patient.analytics', [
                'user' => $user,
                'statusCounts' => $statusCounts,
                'chartData' => $chartData,
            ]);
        })->name('analytics');

        // Patient Notification Routes
        Route::get('/notifications', [PatientController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [PatientController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [PatientController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [PatientController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/access-request/respond', [PatientController::class, 'respondToAccessRequest'])->name('respond-access');
        Route::post('/access-request/revoke', [PatientController::class, 'revokeAccess'])->name('revoke-access');
        Route::get('/access-requests', [PatientController::class, 'getAccessRequests'])->name('get-access-requests');
        Route::get('/granted-doctors', [PatientController::class, 'getGrantedDoctors'])->name('get-granted-doctors');
        Route::post('/block-doctor', [PatientController::class, 'blockDoctor'])->name('block-doctor');
        Route::post('/unblock-doctor', [PatientController::class, 'unblockDoctor'])->name('unblock-doctor');
        Route::get('/blocked-doctors', [PatientController::class, 'getBlockedDoctors'])->name('get-blocked-doctors');

        // Patient Exam Requests Routes
        Route::get('/exam-requests', [PatientController::class, 'getExamRequests'])
            ->name('get-exam-requests');

        Route::get('/exam-requests/{examRequest}', [PatientController::class, 'getExamRequest'])
            ->name('exam-request-detail');

        // Patient chooses laboratory
        Route::get(
            '/exam-requests/{examRequest}/choose-laboratory',
            [PatientController::class, 'chooseLaboratory']
        )
            ->name('choose-laboratory');

        Route::post(
            '/exam-requests/{examRequest}/assign-laboratory',
            [PatientController::class, 'assignLaboratory']
        )
            ->name('assign-laboratory');

        // Medical History Timeline (Task 3.5)
        Route::get('/medical-history', [PatientController::class, 'medicalHistory'])
            ->name('medical-history');

        // PDF / Print export for a completed exam request (Task 3.2)
        Route::get('/exam-requests/{examRequest}/print', [PatientController::class, 'printExamRequest'])
            ->name('print-exam-request');

        // Cancel exam request
        Route::post('/exam-requests/{examRequest}/cancel', [PatientController::class, 'cancelExamRequest'])
            ->name('cancel-exam-request');

        // TIER 1.5 — Patient Health Trends
        Route::get('/health-trends', [PatientController::class, 'healthTrends'])->name('health-trends');
        Route::get('/api/health-trends-data', [PatientController::class, 'healthTrendsData'])->name('health-trends-data');

        // TIER 2.2 — Patient-Doctor Chat
        Route::get('/chat/{doctor}', [PatientController::class, 'chat'])->name('chat');
        Route::get('/chat/{doctor}/messages', [PatientController::class, 'chatMessages'])->name('chat-messages');
        Route::post('/chat/{doctor}/send', [PatientController::class, 'chatSend'])->name('chat-send');
        Route::get('/chat/unread-count', [PatientController::class, 'chatUnreadCount'])->name('chat-unread-count');

        //         TIER 2.4 — Multi-Lab Splitting
        Route::get('/exam-requests/{examRequest}/split-suggestions', [PatientController::class, 'splitSuggestions'])->name('split-suggestions');
        Route::post('/exam-requests/{examRequest}/apply-split', [PatientController::class, 'applySplit'])->name('apply-split');

        // TIER 3.0 — Patient Invoices
        Route::get('/invoices', [PatientController::class, 'invoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [PatientController::class, 'invoiceShow'])->name('invoices.show');
        Route::get('/invoices/{invoice}/print', [PatientController::class, 'printInvoice'])->name('invoices.print');
        Route::post('/invoices/{invoice}/pay', [PatientController::class, 'payInvoice'])->name('invoices.pay');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'patient')->name('logout');
    });

    // Scan doctor QR code — accessible without auth (handles guest redirect internally)
    // Link flow: first view the doctor, then a POST confirms the link to an authenticated patient
    Route::get('/scan/{code}', [PatientController::class, 'scanDoctor'])->name('scan-doctor');
    Route::post('/scan/{code}/link', [PatientController::class, 'linkDoctor'])
        ->middleware('auth')
        ->name('scan-doctor-link');
});

// Center routes
Route::prefix('center')->name('center.')->group(function () {

    // Guest Center Routes
    Route::middleware('guest')->group(function () {

        Route::get('/login', fn () => view('center.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'center');

        Route::get('/register', fn () => view('center.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->middleware('throttle:register')
            ->defaults('role', 'center');

        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'center')
            ->middleware('throttle:password-reset')
            ->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'center')
            ->middleware('throttle:password-reset')
            ->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'center')
            ->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'center')
            ->middleware('throttle:password-reset')
            ->name('password.update');

    });

    // Authenticated Center Routes
    Route::middleware(['auth', 'center', 'throttle:general'])->group(function () {

        // Dashboard
        Route::get(
            '/dashboard',
            [CenterController::class, 'dashboard']
        )->name('dashboard');

        // Exam Requests
        Route::get(
            '/exam-requests',
            [CenterController::class, 'examRequests']
        )->name('exam-requests');

        Route::post(
            '/exam-requests/{examRequest}/claim',
            [CenterController::class, 'claimExamRequest']
        )->name('exam-requests.claim');

        Route::post(
            '/exam-requests/{examRequest}/collect',
            [CenterController::class, 'collectExamRequest']
        )->name('exam-requests.collect');

        // ==========================
        // LAB RESULTS
        // ==========================

        Route::get(
            '/results/{item}/create',
            [LaboResultController::class, 'create']
        )->name('results.create');

        Route::post(
            '/results/{item}',
            [LaboResultController::class, 'store']
        )->name('results.store');

        Route::get(
            '/results/{result}/edit',
            [LaboResultController::class, 'edit']
        )->name('results.edit');

        Route::put(
            '/results/{result}',
            [LaboResultController::class, 'update']
        )->name('results.update');

        // ==========================
        // Machine Integration (HL7)
        // ==========================

        Route::post(
            '/machine/send/{item}',
            [MachineController::class, 'sendToMachine']
        )->name('machine.send');

        Route::get(
            '/machine/status',
            [MachineController::class, 'status']
        )->name('machine.status');

        // Working Hours

        Route::get(
            '/working-hours',
            [CenterController::class, 'workingHours']
        )->name('working-hours');

        Route::post(
            '/working-hours/update',
            [CenterController::class, 'updateWorkingHours']
        )->name('working-hours.update');

        Route::post(
            '/working-hours/exceptions',
            [CenterController::class, 'addException']
        )->name('working-hours.exceptions.store');

        Route::delete(
            '/working-hours/exceptions/{workingHour}',
            [CenterController::class, 'deleteException']
        )->name('working-hours.exceptions.destroy');

        // Consumables & Stock

        Route::get(
            '/consumables',
            [CenterController::class, 'consumables']
        )->name('consumables');

        Route::post(
            '/consumables',
            [CenterController::class, 'storeConsumable']
        )->name('consumables.store');

        Route::put(
            '/consumables/{consumable}',
            [CenterController::class, 'updateConsumable']
        )->name('consumables.update');

        Route::post(
            '/consumables/{consumable}/move',
            [CenterController::class, 'addStockMovement']
        )->name('consumables.move');

        // Equipment

        Route::get(
            '/equipment',
            [CenterController::class, 'equipment']
        )->name('equipment');

        Route::post(
            '/equipment',
            [CenterController::class, 'storeEquipment']
        )->name('equipment.store');

        Route::put(
            '/equipment/{equipment}',
            [CenterController::class, 'updateEquipment']
        )->name('equipment.update');

        Route::post(
            '/equipment/{equipment}/maintenance',
            [CenterController::class, 'storeMaintenance']
        )->name('equipment.maintenance.store');

        Route::put(
            '/equipment/maintenance/{maintenance}',
            [CenterController::class, 'updateMaintenance']
        )->name('equipment.maintenance.update');

        // Notifications
        Route::get('/notifications', [CenterController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [CenterController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [CenterController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [CenterController::class, 'markAllAsRead'])->name('mark-all-read');

        // Available Exams (Center-side)
        Route::get('/available-exams', [CenterController::class, 'availableExams'])->name('available-exams');
        Route::post('/available-exams', [CenterController::class, 'storeAvailableExam'])->name('available-exams.store');
        Route::put('/available-exams/{availableExam}', [CenterController::class, 'updateAvailableExam'])->name('available-exams.update');
        Route::post('/available-exams/{availableExam}/toggle', [CenterController::class, 'toggleAvailableExam'])->name('available-exams.toggle');
        Route::delete('/available-exams/{availableExam}', [CenterController::class, 'destroyAvailableExam'])->name('available-exams.destroy');

        // Machine Configurations (Center-side)
        Route::get('/machine-configurations', [MachineConfigurationController::class, 'index'])->name('machine-configurations.index');
        Route::get('/machine-configurations/create', [MachineConfigurationController::class, 'create'])->name('machine-configurations.create');
        Route::post('/machine-configurations', [MachineConfigurationController::class, 'store'])->name('machine-configurations.store');
        Route::get('/machine-configurations/{machineConfiguration}/edit', [MachineConfigurationController::class, 'edit'])->name('machine-configurations.edit');
        Route::put('/machine-configurations/{machineConfiguration}', [MachineConfigurationController::class, 'update'])->name('machine-configurations.update');
        Route::delete('/machine-configurations/{machineConfiguration}', [MachineConfigurationController::class, 'destroy'])->name('machine-configurations.destroy');
        Route::post('/machine-configurations/{machineConfiguration}/test', [MachineConfigurationController::class, 'test'])->name('machine-configurations.test');

        // ==========================
        // Billing & CNAM
        // ==========================

        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create');
        Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
        Route::get('/billing/{invoice}', [BillingController::class, 'show'])->name('billing.show');
        Route::get('/billing/{invoice}/print', [BillingController::class, 'print'])->name('billing.print');
        Route::get('/billing/{invoice}/traite', [BillingController::class, 'printTraite'])->name('billing.traite');
        Route::post('/billing/{invoice}/pay', [BillingController::class, 'registerPayment'])->name('billing.pay');
        Route::post('/billing/{invoice}/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
        Route::post('/payments/{payment}/confirm', [BillingController::class, 'confirmPayment'])->name('payments.confirm');
        Route::get('/billing/{invoice}/elfatoora', [BillingController::class, 'elFatooraExport'])->name('billing.elfatoora');

        // CNAM Nomenclature Management
        Route::get('/cnam', [BillingController::class, 'cnamIndex'])->name('cnam.index');
        Route::post('/cnam', [BillingController::class, 'cnamStore'])->name('cnam.store');

        // ==========================
        // Sample Tracking
        // ==========================

        Route::get('/samples', [SampleController::class, 'index'])->name('samples.index');
        Route::get('/samples/create', [SampleController::class, 'create'])->name('samples.create');
        Route::post('/samples', [SampleController::class, 'store'])->name('samples.store');
        Route::get('/samples/scan', [SampleController::class, 'scan'])->name('samples.scan');
        Route::post('/samples/lookup', [SampleController::class, 'lookupByBarcode'])->name('samples.lookup');
        Route::get('/samples/{sample}', [SampleController::class, 'show'])->name('samples.show');
        Route::post('/samples/{sample}/status', [SampleController::class, 'updateStatus'])->name('samples.status');
        Route::get('/samples/{sample}/barcode', [SampleController::class, 'printBarcode'])->name('samples.barcode');

        // Logout

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        )
            ->defaults('role', 'center')
            ->name('logout');

    });

});

// Admin routes
// Admins gate every CRUD route with the permission middleware tied to the group's granted actions
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'admin', 'throttle:general'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        // Exams CRUD
        Route::get('/exams', [AdminController::class, 'exams'])->name('exams.index');

        Route::get('/exams/create', [AdminController::class, 'createExam'])
            ->name('exams.create');

        Route::post('/exams', [AdminController::class, 'storeExam'])
            ->name('exams.store');

        Route::get('/exams/{exam}/edit', [AdminController::class, 'editExam'])
            ->name('exams.edit');
        Route::get('/exams/{exam}', [AdminController::class, 'showExam'])
            ->name('exams.show');
        Route::put('/exams/{exam}', [AdminController::class, 'updateExam'])
            ->name('exams.update');

        Route::patch('/exams/{exam}/archive', [AdminController::class, 'archiveExam'])
            ->name('exams.archive');
        Route::delete('/exams/{exam}/force', [AdminController::class, 'forceDeleteExam'])
            ->name('exams.force-delete');
        Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])
            ->defaults('role', 'admin')
            ->name('logout');
        // Laboratories CRUD (permission-checked, e.g. view-laboratories)
        Route::get('/laboratories', [LaboratoryController::class, 'index'])->name('laboratories.index')->middleware('permission:view-laboratories');
        Route::get('/laboratories/create', [LaboratoryController::class, 'create'])->name('laboratories.create')->middleware('permission:add-laboratory');
        Route::post('/laboratories', [LaboratoryController::class, 'store'])->name('laboratories.store')->middleware('permission:add-laboratory');
        Route::get('/laboratories/{laboratory}/edit', [LaboratoryController::class, 'edit'])->name('laboratories.edit')->middleware('permission:modify-laboratory');
        Route::put('/laboratories/{laboratory}', [LaboratoryController::class, 'update'])->name('laboratories.update')->middleware('permission:modify-laboratory');
        Route::delete('/laboratories/{laboratory}', [LaboratoryController::class, 'destroy'])->name('laboratories.destroy')->middleware('permission:delete-laboratory');
        Route::delete('/laboratories/{laboratory}/force', [LaboratoryController::class, 'forceDelete'])->name('laboratories.force-delete')->middleware('permission:delete-laboratory');

        // Users CRUD
        Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view-users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-users');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');
        Route::delete('/users/{user}/force', [UserController::class, 'forceDelete'])->name('users.force-delete')->middleware('permission:delete-users');

        // Groups CRUD
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index')->middleware('permission:view-groups');
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create')->middleware('permission:create-groups');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store')->middleware('permission:create-groups');
        Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit')->middleware('permission:edit-groups');
        Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update')->middleware('permission:edit-groups');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy')->middleware('permission:delete-groups');
        Route::delete('/groups/{group}/force', [GroupController::class, 'forceDelete'])->name('groups.force-delete')->middleware('permission:delete-groups');

        // Audit trail (activity logs)
        Route::get('/activity', [ActivityLogController::class, 'index'])
            ->name('activity')
            ->middleware('permission:view-activity');

        // GDPR / RGPD (export & erasure requests)
        Route::get('/gdpr', [GdprController::class, 'index'])
            ->name('gdpr')
            ->middleware('permission:view-gdpr');
        Route::get('/gdpr/export/{user}', [GdprController::class, 'export'])
            ->name('gdpr.export')
            ->middleware('permission:manage-gdpr');
        Route::post('/gdpr/erase/{user}', [GdprController::class, 'erase'])
            ->name('gdpr.erase')
            ->middleware('permission:manage-gdpr');

        // Features CRUD
        Route::get('/features', [FeatureController::class, 'index'])->name('features.index')->middleware('permission:view-features');
        Route::get('/features/create', [FeatureController::class, 'create'])->name('features.create')->middleware('permission:create-features');
        Route::post('/features', [FeatureController::class, 'store'])->name('features.store')->middleware('permission:create-features');
        Route::get('/features/{feature}/edit', [FeatureController::class, 'edit'])->name('features.edit')->middleware('permission:edit-features');
        Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('features.update')->middleware('permission:edit-features');
        Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy')->middleware('permission:delete-features');
        Route::delete('/features/{feature}/force', [FeatureController::class, 'forceDelete'])->name('features.force-delete')->middleware('permission:delete-features');

        // Actions management (nested under Features)
        Route::post('/features/{feature}/actions', [FeatureController::class, 'storeAction'])->name('features.actions.store')->middleware('permission:edit-features');
        Route::put('/actions/{action}', [FeatureController::class, 'updateAction'])->name('actions.update')->middleware('permission:edit-features');
        Route::delete('/actions/{action}', [FeatureController::class, 'destroyAction'])->name('actions.destroy')->middleware('permission:edit-features');
    });
});

// Profile routes (any authenticated user)
Route::middleware(['auth', 'throttle:general'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::get('/two-factor', [TwoFactorController::class, 'showSetup'])->name('two-factor.setup');
    Route::post('/two-factor', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
});

// Two-factor login challenge (guest, completes a pending login)
Route::get('/login/two-factor', [TwoFactorController::class, 'loginChallenge'])->middleware('guest')->name('two-factor.login');
Route::post('/login/two-factor', [TwoFactorController::class, 'loginVerify'])->middleware('guest')->name('two-factor.verify');

// Location routes (AJAX API endpoints)
Route::get('/countries', [LocationController::class, 'getCountries'])->middleware('throttle:location')->name('countries.index');
Route::get('/countries/{country}/states', [LocationController::class, 'getStates'])->middleware('throttle:location')->name('countries.states');

Route::middleware('auth')->group(function () {
    // Old profile routes removed — using ProfileController routes above
});

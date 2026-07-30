<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaboResultController;
use App\Http\Controllers\MachineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;



// Landing Selection Portal
Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->admin)
            return redirect()->route('admin.dashboard');
        if ($user->doctor)
            return redirect()->route('doctor.dashboard');
        if ($user->patient)
            return redirect()->route('patient.dashboard');
        if ($user->staff)
            return redirect()->route('center.dashboard');
    }
    return view('portal');
})->name('home');

// Doctor Authentication Pages
Route::prefix('doctor')->name('doctor.')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::get('/login', fn() => view('doctor.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'doctor');

        Route::get('/register', fn() => view('doctor.register'))
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

    Route::middleware(['auth', 'doctor', 'throttle:general'])->group(function () {
        Route::get('/dashboard', function () {
            $user = auth()->user();
            // Verify they are a doctor
            if (!$user->doctor) {
                return redirect()->route('home');
            }
            $doctor = $user->doctor;

            $recentPatients = \App\Models\DoctorPatientAccess::where('doctor_id', $doctor->id)
                ->where('access_status', 'granted')
                ->with('patient.user')
                ->latest('updated_at')
                ->limit(20)
                ->get();

            // Search / Filter on Exam Requests (Task 4.2)
            $search = request('search');
            $status = request('status');

            $query = \App\Models\ExamRequest::where('doctor_id', $doctor->id)
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

            // Analytics: monthly prescriptions for last 6 months
            $monthlyPrescriptions = \App\Models\ExamRequest::where('doctor_id', $doctor->id)
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, DATE_FORMAT(created_at, '%b %Y') as label, COUNT(*) as count")
                ->groupBy('month_key', 'label')
                ->orderBy('month_key')
                ->get();

            $allMonths = collect();
            for ($i = 5; $i >= 0; $i--) {
                $d = now()->subMonths($i);
                $allMonths->push([
                    'key'   => $d->format('Y-m'),
                    'label' => $d->format('M Y'),
                    'count' => 0,
                ]);
            }
            $chartData = $allMonths->map(function ($m) use ($monthlyPrescriptions) {
                $found = $monthlyPrescriptions->firstWhere('key', $m['key']);
                $m['count'] = $found ? $found->count : 0;
                return $m;
            })->values();

            // Analytics: unique patients seen
            $uniquePatientsCount = \App\Models\ExamRequest::where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id');

            // Analytics: completion rate
            $totalRequests = \App\Models\ExamRequest::where('doctor_id', $doctor->id)->count();
            $completedRequests = \App\Models\ExamRequest::where('doctor_id', $doctor->id)
                ->where('status', 'completed')
                ->count();
            $completionRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) : 0;

            return view('doctor.dashboard', compact(
                'user', 'recentPatients', 'recentExams', 'search', 'status',
                'chartData', 'uniquePatientsCount', 'completionRate'
            ));
        })->name('dashboard');

        // QR Code scan → auto-search patient
        Route::get('/scan/{code}', function ($code) {
            return redirect()->route('doctor.dashboard', ['scan' => $code]);
        })->name('scan-patient');

        // Doctor Interface Routes
        Route::get('/patient-search', [\App\Http\Controllers\DoctorController::class, 'patientSearch'])->name('patient-search');
        Route::post('/search-patient', [\App\Http\Controllers\DoctorController::class, 'searchPatient'])->name('search-patient');
        Route::post('/request-access', [\App\Http\Controllers\DoctorController::class, 'requestAccess'])->name('request-access');
        Route::get('/exams-selection/{patient}', [\App\Http\Controllers\DoctorController::class, 'selectExams'])->name('select-exams');
        Route::post('/create-exam-request', [\App\Http\Controllers\DoctorController::class, 'createExamRequest'])->name('create-exam-request');
        Route::get('/my-patients', [\App\Http\Controllers\DoctorController::class, 'myPatients'])->name('my-patients');
        Route::post('/exam-requests/{examRequest}/submit-interpretation', [\App\Http\Controllers\DoctorController::class, 'submitInterpretation'])->name('submit-interpretation');
        Route::post('/apply-exam-group', [\App\Http\Controllers\DoctorController::class, 'applyExamGroup'])->name('apply-exam-group');
        Route::post('/api/exam-groups', [\App\Http\Controllers\DoctorController::class, 'storeExamGroupApi'])->name('api.store-exam-group');

        // Exam Groups CRUD (dedicated pages)
        Route::get('/exam-groups', [\App\Http\Controllers\DoctorController::class, 'examGroupsIndex'])->name('exam-groups.index');
        Route::get('/exam-groups/create', [\App\Http\Controllers\DoctorController::class, 'examGroupsCreate'])->name('exam-groups.create');
        Route::post('/exam-groups', [\App\Http\Controllers\DoctorController::class, 'examGroupsStore'])->name('exam-groups.store');
        Route::get('/exam-groups/{examGroup}/edit', [\App\Http\Controllers\DoctorController::class, 'examGroupsEdit'])->name('exam-groups.edit');
        Route::put('/exam-groups/{examGroup}', [\App\Http\Controllers\DoctorController::class, 'examGroupsUpdate'])->name('exam-groups.update');
        Route::delete('/exam-groups/{examGroup}', [\App\Http\Controllers\DoctorController::class, 'examGroupsDestroy'])->name('exam-groups.destroy');

        // PDF / Print export for a completed exam request (Task 3.2)
        Route::get('/exam-requests/{examRequest}/print', [\App\Http\Controllers\DoctorController::class, 'printExamRequest'])
            ->name('print-exam-request');

        // Cancel exam request
        Route::post('/exam-requests/{examRequest}/cancel', [\App\Http\Controllers\DoctorController::class, 'cancelExamRequest'])
            ->name('cancel-exam-request');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\DoctorController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\DoctorController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\DoctorController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\DoctorController::class, 'markAllAsRead'])->name('mark-all-read');

        // TIER 1.4 — Smart Exam Suggestions
        Route::get('/api/smart-suggestions/{patient}', [\App\Http\Controllers\DoctorController::class, 'smartSuggestions'])->name('smart-suggestions');
        Route::get('/api/patient-health-trends/{patient}', [\App\Http\Controllers\DoctorController::class, 'patientHealthTrends'])->name('patient-health-trends');

        // TIER 2.2 — Doctor-Patient Chat
        Route::get('/chat/{patient}', [\App\Http\Controllers\DoctorController::class, 'chat'])->name('chat');
        Route::get('/chat/{patient}/messages', [\App\Http\Controllers\DoctorController::class, 'chatMessages'])->name('chat-messages');
        Route::post('/chat/{patient}/send', [\App\Http\Controllers\DoctorController::class, 'chatSend'])->name('chat-send');
        Route::get('/chat/unread-count', [\App\Http\Controllers\DoctorController::class, 'chatUnreadCount'])->name('chat-unread-count');

        // Patient Medical Records
        Route::get('/patients/{patient}/medical-records', [\App\Http\Controllers\DoctorController::class, 'medicalRecords'])->name('medical-records');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'doctor')->name('logout');
    });
});

// Patient Authentication Pages
Route::prefix('patient')->name('patient.')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::get('/login', fn() => view('patient.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'patient');

        Route::get('/register', fn() => view('patient.register'))
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

    Route::middleware(['auth', 'patient', 'throttle:general'])->group(function () {
        Route::get('/dashboard', function () {
            if (!auth()->user()->patient) {
                return redirect()->route('home');
            }
            return view('patient.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

        Route::get('/analytics', function () {
            if (!auth()->user()->patient) {
                return redirect()->route('home');
            }
            $user = auth()->user();
            $patient = $user->patient;

            $statusCounts = $patient->examRequests()
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $monthlyData = $patient->examRequests()
                ->where('created_at', '>=', now()->subMonths(6))
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as count")
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
        Route::get('/notifications', [\App\Http\Controllers\PatientController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\PatientController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\PatientController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\PatientController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/access-request/respond', [\App\Http\Controllers\PatientController::class, 'respondToAccessRequest'])->name('respond-access');
        Route::post('/access-request/revoke', [\App\Http\Controllers\PatientController::class, 'revokeAccess'])->name('revoke-access');
        Route::get('/access-requests', [\App\Http\Controllers\PatientController::class, 'getAccessRequests'])->name('get-access-requests');
        Route::get('/granted-doctors', [\App\Http\Controllers\PatientController::class, 'getGrantedDoctors'])->name('get-granted-doctors');
        Route::post('/block-doctor', [\App\Http\Controllers\PatientController::class, 'blockDoctor'])->name('block-doctor');
        Route::post('/unblock-doctor', [\App\Http\Controllers\PatientController::class, 'unblockDoctor'])->name('unblock-doctor');
        Route::get('/blocked-doctors', [\App\Http\Controllers\PatientController::class, 'getBlockedDoctors'])->name('get-blocked-doctors');

        // Patient Exam Requests Routes
        Route::get('/exam-requests', [\App\Http\Controllers\PatientController::class, 'getExamRequests'])
            ->name('get-exam-requests');

        Route::get('/exam-requests/{examRequest}', [\App\Http\Controllers\PatientController::class, 'getExamRequest'])
            ->name('exam-request-detail');


        // Patient chooses laboratory
        Route::get(
            '/exam-requests/{examRequest}/choose-laboratory',
            [\App\Http\Controllers\PatientController::class, 'chooseLaboratory']
        )
            ->name('choose-laboratory');


        Route::post(
            '/exam-requests/{examRequest}/assign-laboratory',
            [\App\Http\Controllers\PatientController::class, 'assignLaboratory']
        )
            ->name('assign-laboratory');

        // Medical History Timeline (Task 3.5)
        Route::get('/medical-history', [\App\Http\Controllers\PatientController::class, 'medicalHistory'])
            ->name('medical-history');

        // PDF / Print export for a completed exam request (Task 3.2)
        Route::get('/exam-requests/{examRequest}/print', [\App\Http\Controllers\PatientController::class, 'printExamRequest'])
            ->name('print-exam-request');

        // Cancel exam request
        Route::post('/exam-requests/{examRequest}/cancel', [\App\Http\Controllers\PatientController::class, 'cancelExamRequest'])
            ->name('cancel-exam-request');

        // TIER 1.5 — Patient Health Trends
        Route::get('/health-trends', [\App\Http\Controllers\PatientController::class, 'healthTrends'])->name('health-trends');
        Route::get('/api/health-trends-data', [\App\Http\Controllers\PatientController::class, 'healthTrendsData'])->name('health-trends-data');

        // TIER 2.2 — Patient-Doctor Chat
        Route::get('/chat/{doctor}', [\App\Http\Controllers\PatientController::class, 'chat'])->name('chat');
        Route::get('/chat/{doctor}/messages', [\App\Http\Controllers\PatientController::class, 'chatMessages'])->name('chat-messages');
        Route::post('/chat/{doctor}/send', [\App\Http\Controllers\PatientController::class, 'chatSend'])->name('chat-send');
        Route::get('/chat/unread-count', [\App\Http\Controllers\PatientController::class, 'chatUnreadCount'])->name('chat-unread-count');

        //         TIER 2.4 — Multi-Lab Splitting
        Route::get('/exam-requests/{examRequest}/split-suggestions', [\App\Http\Controllers\PatientController::class, 'splitSuggestions'])->name('split-suggestions');
        Route::post('/exam-requests/{examRequest}/apply-split', [\App\Http\Controllers\PatientController::class, 'applySplit'])->name('apply-split');

        // Scan doctor QR code → auto-link
        Route::get('/scan/{code}', [\App\Http\Controllers\PatientController::class, 'scanDoctor'])->name('scan-doctor');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'patient')->name('logout');
    });
});

// Medical Center Authentication Pages
Route::prefix('center')->name('center.')->group(function () {


    // Guest Center Routes
    Route::middleware('guest')->group(function () {

        Route::get('/login', fn() => view('center.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:login')
            ->defaults('role', 'center');


        Route::get('/register', fn() => view('center.register'))
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
            [\App\Http\Controllers\CenterController::class, 'dashboard']
        )->name('dashboard');



        // Exam Requests
        Route::get(
            '/exam-requests',
            [\App\Http\Controllers\CenterController::class, 'examRequests']
        )->name('exam-requests');


        Route::post(
            '/exam-requests/{examRequest}/claim',
            [\App\Http\Controllers\CenterController::class, 'claimExamRequest']
        )->name('exam-requests.claim');

        Route::post(
            '/exam-requests/{examRequest}/collect',
            [\App\Http\Controllers\CenterController::class, 'collectExamRequest']
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
            [\App\Http\Controllers\CenterController::class, 'workingHours']
        )->name('working-hours');


        Route::post(
            '/working-hours/update',
            [\App\Http\Controllers\CenterController::class, 'updateWorkingHours']
        )->name('working-hours.update');


        Route::post(
            '/working-hours/exceptions',
            [\App\Http\Controllers\CenterController::class, 'addException']
        )->name('working-hours.exceptions.store');


        Route::delete(
            '/working-hours/exceptions/{workingHour}',
            [\App\Http\Controllers\CenterController::class, 'deleteException']
        )->name('working-hours.exceptions.destroy');





        // Consumables & Stock

        Route::get(
            '/consumables',
            [\App\Http\Controllers\CenterController::class, 'consumables']
        )->name('consumables');


        Route::post(
            '/consumables',
            [\App\Http\Controllers\CenterController::class, 'storeConsumable']
        )->name('consumables.store');


        Route::put(
            '/consumables/{consumable}',
            [\App\Http\Controllers\CenterController::class, 'updateConsumable']
        )->name('consumables.update');


        Route::post(
            '/consumables/{consumable}/move',
            [\App\Http\Controllers\CenterController::class, 'addStockMovement']
        )->name('consumables.move');





        // Equipment

        Route::get(
            '/equipment',
            [\App\Http\Controllers\CenterController::class, 'equipment']
        )->name('equipment');


        Route::post(
            '/equipment',
            [\App\Http\Controllers\CenterController::class, 'storeEquipment']
        )->name('equipment.store');


        Route::put(
            '/equipment/{equipment}',
            [\App\Http\Controllers\CenterController::class, 'updateEquipment']
        )->name('equipment.update');


        Route::post(
            '/equipment/{equipment}/maintenance',
            [\App\Http\Controllers\CenterController::class, 'storeMaintenance']
        )->name('equipment.maintenance.store');


        Route::put(
            '/equipment/maintenance/{maintenance}',
            [\App\Http\Controllers\CenterController::class, 'updateMaintenance']
        )->name('equipment.maintenance.update');





        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\CenterController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\CenterController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\CenterController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\CenterController::class, 'markAllAsRead'])->name('mark-all-read');

        // Available Exams (Center-side)
        Route::get('/available-exams', [\App\Http\Controllers\CenterController::class, 'availableExams'])->name('available-exams');
        Route::post('/available-exams', [\App\Http\Controllers\CenterController::class, 'storeAvailableExam'])->name('available-exams.store');
        Route::put('/available-exams/{availableExam}', [\App\Http\Controllers\CenterController::class, 'updateAvailableExam'])->name('available-exams.update');
        Route::post('/available-exams/{availableExam}/toggle', [\App\Http\Controllers\CenterController::class, 'toggleAvailableExam'])->name('available-exams.toggle');
        Route::delete('/available-exams/{availableExam}', [\App\Http\Controllers\CenterController::class, 'destroyAvailableExam'])->name('available-exams.destroy');

        // Machine Configurations (Center-side)
        Route::get('/machine-configurations', [\App\Http\Controllers\MachineConfigurationController::class, 'index'])->name('machine-configurations.index');
        Route::get('/machine-configurations/create', [\App\Http\Controllers\MachineConfigurationController::class, 'create'])->name('machine-configurations.create');
        Route::post('/machine-configurations', [\App\Http\Controllers\MachineConfigurationController::class, 'store'])->name('machine-configurations.store');
        Route::get('/machine-configurations/{machineConfiguration}/edit', [\App\Http\Controllers\MachineConfigurationController::class, 'edit'])->name('machine-configurations.edit');
        Route::put('/machine-configurations/{machineConfiguration}', [\App\Http\Controllers\MachineConfigurationController::class, 'update'])->name('machine-configurations.update');
        Route::delete('/machine-configurations/{machineConfiguration}', [\App\Http\Controllers\MachineConfigurationController::class, 'destroy'])->name('machine-configurations.destroy');
        Route::post('/machine-configurations/{machineConfiguration}/test', [\App\Http\Controllers\MachineConfigurationController::class, 'test'])->name('machine-configurations.test');

        // Logout

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        )
            ->defaults('role', 'center')
            ->name('logout');

    });

});

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\AvailableExamController;

// Admin Pages
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
        // Laboratories CRUD
        Route::get('/laboratories', [\App\Http\Controllers\LaboratoryController::class, 'index'])->name('laboratories.index')->middleware('permission:view-laboratories');
        Route::get('/laboratories/create', [\App\Http\Controllers\LaboratoryController::class, 'create'])->name('laboratories.create')->middleware('permission:add-laboratory');
        Route::post('/laboratories', [\App\Http\Controllers\LaboratoryController::class, 'store'])->name('laboratories.store')->middleware('permission:add-laboratory');
        Route::get('/laboratories/{laboratory}/edit', [\App\Http\Controllers\LaboratoryController::class, 'edit'])->name('laboratories.edit')->middleware('permission:modify-laboratory');
        Route::put('/laboratories/{laboratory}', [\App\Http\Controllers\LaboratoryController::class, 'update'])->name('laboratories.update')->middleware('permission:modify-laboratory');
        Route::delete('/laboratories/{laboratory}', [\App\Http\Controllers\LaboratoryController::class, 'destroy'])->name('laboratories.destroy')->middleware('permission:delete-laboratory');
        Route::delete('/laboratories/{laboratory}/force', [\App\Http\Controllers\LaboratoryController::class, 'forceDelete'])->name('laboratories.force-delete')->middleware('permission:delete-laboratory');

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

        // Features CRUD
        Route::get('/features', [\App\Http\Controllers\FeatureController::class, 'index'])->name('features.index')->middleware('permission:view-features');
        Route::get('/features/create', [\App\Http\Controllers\FeatureController::class, 'create'])->name('features.create')->middleware('permission:create-features');
        Route::post('/features', [\App\Http\Controllers\FeatureController::class, 'store'])->name('features.store')->middleware('permission:create-features');
        Route::get('/features/{feature}/edit', [\App\Http\Controllers\FeatureController::class, 'edit'])->name('features.edit')->middleware('permission:edit-features');
        Route::put('/features/{feature}', [\App\Http\Controllers\FeatureController::class, 'update'])->name('features.update')->middleware('permission:edit-features');
        Route::delete('/features/{feature}', [\App\Http\Controllers\FeatureController::class, 'destroy'])->name('features.destroy')->middleware('permission:delete-features');
        Route::delete('/features/{feature}/force', [\App\Http\Controllers\FeatureController::class, 'forceDelete'])->name('features.force-delete')->middleware('permission:delete-features');

        // Actions management (nested under Features)
        Route::post('/features/{feature}/actions', [\App\Http\Controllers\FeatureController::class, 'storeAction'])->name('features.actions.store')->middleware('permission:edit-features');
        Route::put('/actions/{action}', [\App\Http\Controllers\FeatureController::class, 'updateAction'])->name('actions.update')->middleware('permission:edit-features');
        Route::delete('/actions/{action}', [\App\Http\Controllers\FeatureController::class, 'destroyAction'])->name('actions.destroy')->middleware('permission:edit-features');
    });
});

// Profile routes (any authenticated user)
Route::middleware(['auth', 'throttle:general'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
    Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password');
});

// Location routes (AJAX API endpoints)
Route::get('/countries', [\App\Http\Controllers\LocationController::class, 'getCountries'])->middleware('throttle:location')->name('countries.index');
Route::get('/countries/{country}/states', [\App\Http\Controllers\LocationController::class, 'getStates'])->middleware('throttle:location')->name('countries.states');

Route::middleware('auth')->group(function () {
    // Old profile routes removed — using ProfileController routes above
});


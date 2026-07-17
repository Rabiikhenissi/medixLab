<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaboResultController;

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
            ->defaults('role', 'doctor');

        Route::get('/register', fn() => view('doctor.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->defaults('role', 'doctor');


        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'doctor')
            ->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'doctor')
            ->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'doctor')
            ->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'doctor')
            ->name('password.update');
    });

    Route::middleware('auth')->group(function () {
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
            $recentExams = \App\Models\ExamRequest::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'items.exam', 'items.resultLabo.details'])
                ->latest('created_at')
                ->limit(20)
                ->get();

            return view('doctor.dashboard', compact('user', 'recentPatients', 'recentExams'));
        })->name('dashboard');

        // Doctor Interface Routes
        Route::get('/patient-search', [\App\Http\Controllers\DoctorController::class, 'patientSearch'])->name('patient-search');
        Route::post('/search-patient', [\App\Http\Controllers\DoctorController::class, 'searchPatient'])->name('search-patient');
        Route::post('/request-access', [\App\Http\Controllers\DoctorController::class, 'requestAccess'])->name('request-access');
        Route::get('/exams-selection/{patient}', [\App\Http\Controllers\DoctorController::class, 'selectExams'])->name('select-exams');
        Route::post('/create-exam-request', [\App\Http\Controllers\DoctorController::class, 'createExamRequest'])->name('create-exam-request');
        Route::post('/exam-requests/{examRequest}/submit-interpretation', [\App\Http\Controllers\DoctorController::class, 'submitInterpretation'])->name('submit-interpretation');
        Route::post('/apply-exam-group', [\App\Http\Controllers\DoctorController::class, 'applyExamGroup'])->name('apply-exam-group');

        // Exam Groups CRUD (dedicated pages)
        Route::get('/exam-groups', [\App\Http\Controllers\DoctorController::class, 'examGroupsIndex'])->name('exam-groups.index');
        Route::get('/exam-groups/create', [\App\Http\Controllers\DoctorController::class, 'examGroupsCreate'])->name('exam-groups.create');
        Route::post('/exam-groups', [\App\Http\Controllers\DoctorController::class, 'examGroupsStore'])->name('exam-groups.store');
        Route::get('/exam-groups/{examGroup}/edit', [\App\Http\Controllers\DoctorController::class, 'examGroupsEdit'])->name('exam-groups.edit');
        Route::put('/exam-groups/{examGroup}', [\App\Http\Controllers\DoctorController::class, 'examGroupsUpdate'])->name('exam-groups.update');
        Route::delete('/exam-groups/{examGroup}', [\App\Http\Controllers\DoctorController::class, 'examGroupsDestroy'])->name('exam-groups.destroy');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'doctor')->name('logout');
    });
});

// Patient Authentication Pages
Route::prefix('patient')->name('patient.')->group(function () {

    Route::middleware('guest')->group(function () {

        Route::get('/login', fn() => view('patient.login'))
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->defaults('role', 'patient');

        Route::get('/register', fn() => view('patient.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->defaults('role', 'patient');


        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'patient')
            ->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'patient')
            ->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'patient')
            ->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'patient')
            ->name('password.update');

    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            // Verify they are a patient
            if (!auth()->user()->patient) {
                return redirect()->route('home');
            }
            return view('patient.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

        // Patient Notification Routes
        Route::get('/notifications', [\App\Http\Controllers\PatientController::class, 'getNotifications'])->name('get-notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\PatientController::class, 'getUnreadCount'])->name('unread-count');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\PatientController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/access-request/respond', [\App\Http\Controllers\PatientController::class, 'respondToAccessRequest'])->name('respond-access');
        Route::get('/access-requests', [\App\Http\Controllers\PatientController::class, 'getAccessRequests'])->name('get-access-requests');

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
            ->defaults('role', 'center');


        Route::get('/register', fn() => view('center.register'))
            ->name('register');

        Route::post('/register', [AuthController::class, 'register'])
            ->defaults('role', 'center');


        // Password Reset
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
            ->defaults('role', 'center')
            ->name('password.request');


        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
            ->defaults('role', 'center')
            ->name('password.email');


        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
            ->defaults('role', 'center')
            ->name('password.reset');


        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->defaults('role', 'center')
            ->name('password.update');

    });





    // Authenticated Center Routes
    Route::middleware('auth')->group(function () {


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

// Admin Pages
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
        Route::put('/exams/{exam}', [AdminController::class, 'updateExam'])->name('exams.update');
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

        // Users CRUD
        Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:view-users');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:create-users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:create-users');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit-users');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:edit-users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete-users');

        // Groups CRUD
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index')->middleware('permission:view-groups');
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create')->middleware('permission:create-groups');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store')->middleware('permission:create-groups');
        Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit')->middleware('permission:edit-groups');
        Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update')->middleware('permission:edit-groups');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy')->middleware('permission:delete-groups');

        // Features CRUD
        Route::get('/features', [\App\Http\Controllers\FeatureController::class, 'index'])->name('features.index')->middleware('permission:view-features');
        Route::get('/features/create', [\App\Http\Controllers\FeatureController::class, 'create'])->name('features.create')->middleware('permission:create-features');
        Route::post('/features', [\App\Http\Controllers\FeatureController::class, 'store'])->name('features.store')->middleware('permission:create-features');
        Route::get('/features/{feature}/edit', [\App\Http\Controllers\FeatureController::class, 'edit'])->name('features.edit')->middleware('permission:edit-features');
        Route::put('/features/{feature}', [\App\Http\Controllers\FeatureController::class, 'update'])->name('features.update')->middleware('permission:edit-features');
        Route::delete('/features/{feature}', [\App\Http\Controllers\FeatureController::class, 'destroy'])->name('features.destroy')->middleware('permission:delete-features');

        // Actions management (nested under Features)
        Route::post('/features/{feature}/actions', [\App\Http\Controllers\FeatureController::class, 'storeAction'])->name('features.actions.store')->middleware('permission:edit-features');
        Route::put('/actions/{action}', [\App\Http\Controllers\FeatureController::class, 'updateAction'])->name('actions.update')->middleware('permission:edit-features');
        Route::delete('/actions/{action}', [\App\Http\Controllers\FeatureController::class, 'destroyAction'])->name('actions.destroy')->middleware('permission:edit-features');
    });
});

// Location routes (AJAX API endpoints)
Route::get('/countries', [\App\Http\Controllers\LocationController::class, 'getCountries'])->name('countries.index');
Route::get('/countries/{country}/states', [\App\Http\Controllers\LocationController::class, 'getStates'])->name('countries.states');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
});


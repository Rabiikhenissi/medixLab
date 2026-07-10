<?php

use Illuminate\Support\Facades\Route;

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
        if ($user->admin) return redirect()->route('admin.dashboard');
        if ($user->doctor) return redirect()->route('doctor.dashboard');
        if ($user->patient) return redirect()->route('patient.dashboard');
        if ($user->staff) return redirect()->route('center.dashboard');
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
                ->with(['patient.user', 'items.exam'])
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
        Route::get('/exam-requests', [\App\Http\Controllers\PatientController::class, 'getExamRequests'])->name('get-exam-requests');
        Route::get('/exam-requests/{examRequest}', [\App\Http\Controllers\PatientController::class, 'getExamRequest'])->name('exam-request-detail');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'patient')->name('logout');
    });
});

/// Medical Center Authentication Pages
Route::prefix('center')->name('center.')->group(function () {

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


    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            // Verify they are staff
            if (!auth()->user()->staff) {
                return redirect()->route('home');
            }
            return view('center.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'center')->name('logout');
    });
});

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;

// Admin Pages
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/exams', [AdminController::class, 'storeExam'])->name('exams.store');
        Route::put('/exams/{exam}', [AdminController::class, 'updateExam'])->name('exams.update');
        Route::patch('/exams/{exam}/archive', [AdminController::class, 'archiveExam'])->name('exams.archive');
        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'admin')->name('logout');

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
    });
});

// Location routes (AJAX API endpoints)
Route::get('/countries', [\App\Http\Controllers\LocationController::class, 'getCountries'])->name('countries.index');
Route::get('/countries/{country}/states', [\App\Http\Controllers\LocationController::class, 'getStates'])->name('countries.states');


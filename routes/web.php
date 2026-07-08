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
        Route::get('/login', function () {
            return view('doctor.login');
        })->name('login');

        Route::post('/login', [AuthController::class, 'login'])->defaults('role', 'doctor');

        Route::get('/register', function () {
            return view('doctor.register');
        })->name('register');

        Route::post('/register', [AuthController::class, 'register'])->defaults('role', 'doctor');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            // Verify they are a doctor
            if (!auth()->user()->doctor) {
                return redirect()->route('home');
            }
            return view('doctor.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'doctor')->name('logout');
    });
});

// Patient Authentication Pages
Route::prefix('patient')->name('patient.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', function () {
            return view('patient.login');
        })->name('login');

        Route::post('/login', [AuthController::class, 'login'])->defaults('role', 'patient');

        Route::get('/register', function () {
            return view('patient.register');
        })->name('register');

        Route::post('/register', [AuthController::class, 'register'])->defaults('role', 'patient');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {
            // Verify they are a patient
            if (!auth()->user()->patient) {
                return redirect()->route('home');
            }
            return view('patient.dashboard', ['user' => auth()->user()]);
        })->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])->defaults('role', 'patient')->name('logout');
    });
});

// Medical Center Authentication Pages
Route::prefix('center')->name('center.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', function () {
            return view('center.login');
        })->name('login');

        Route::post('/login', [AuthController::class, 'login'])->defaults('role', 'center');

        Route::get('/register', function () {
            return view('center.register');
        })->name('register');

        Route::post('/register', [AuthController::class, 'register'])->defaults('role', 'center');
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

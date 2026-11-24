<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\EmployeeController;

// ==========================================
// ADMINISTRATION
// ==========================================

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard.admin');
        })->name('dashboard');

        // Gestion des employés
        Route::post('/employees/{employee}/activate', [EmployeeController::class, 'activate'])
            ->name('employees.activate');

        Route::resource('employees', EmployeeController::class);

        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);

        Route::resource('teams', \App\Http\Controllers\Admin\TeamController::class);
    });


// ==========================================
// MANAGER
// ==========================================

Route::middleware(['auth', 'active', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard.manager');
        })->name('dashboard');
    });


// ==========================================
// EMPLOYEE
// ==========================================

Route::middleware(['auth', 'active', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard.employee');
        })->name('dashboard');
    });


// ==========================================
// PAGE D'ACCUEIL
// ==========================================

Route::get('/', function () {
    return redirect()->route('login');
});


// ==========================================
// REDIRECTION DU DASHBOARD
// ==========================================

Route::middleware(['auth', 'active'])
    ->get('/dashboard', function () {

        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isManager()) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('employee.dashboard');

    })->name('dashboard');


// ==========================================
// GESTION DES TACHES
// ==========================================

Route::middleware(['auth', 'active'])->group(function () {

    Route::resource('tasks', TaskController::class)
        ->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);

    // Modifier uniquement le statut d'une tâche
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.updateStatus');
});


// ==========================================
// PROFIL UTILISATEUR
// ==========================================

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


// ==========================================
// AUTHENTIFICATION
// ==========================================

require __DIR__ . '/auth.php';

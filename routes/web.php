<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ADMINISTRATION
// ==========================================

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'show'])
            ->name('dashboard');

        // Gestion des employés
        Route::post('/employees/{employee}/activate', [EmployeeController::class, 'activate'])
            ->name('employees.activate');

        Route::resource('employees', EmployeeController::class);

        Route::resource('departments', DepartmentController::class);

        Route::resource('teams', TeamController::class);
    });

// ==========================================
// MANAGER
// ==========================================

Route::middleware(['auth', 'active', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'show'])
            ->name('dashboard');
        Route::get('/issues', [\App\Http\Controllers\IssueController::class, 'index'])
            ->name('issues.index');
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
// ÉTAPE 2 – DEV B : PROJETS
// ==========================================

Route::middleware(['auth', 'active', 'role:manager'])->group(function () {

    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->name('projects.create');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');

    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->name('projects.edit');

    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->name('projects.update');

    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy');

    Route::post('/projects/{project}/members', [ProjectController::class, 'attachMember'])
        ->name('projects.members.store');

    Route::delete('/projects/{project}/members/{user}', [ProjectController::class, 'detachMember'])
        ->name('projects.members.destroy');
});

Route::middleware(['auth', 'active', 'role:admin,manager,employee'])->group(function () {

    Route::get('/projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');
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

    // Delete profile photo
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])
        ->name('profile.avatar.destroy');

    // Delete account
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// ==========================================
// ÉTAPE 3 – DEV B : DEMANDES
// ==========================================

Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/requests', [RequestController::class, 'index'])
        ->name('requests.index');

    Route::get('/requests/create', [RequestController::class, 'create'])
        ->name('requests.create');

    Route::post('/requests', [RequestController::class, 'store'])
        ->name('requests.store');

    Route::get('/requests/{internal_request}', [RequestController::class, 'show'])
        ->name('requests.show');

    Route::post('/requests/{internal_request}/replies', [RequestController::class, 'reply'])
        ->name('requests.reply');

    Route::patch('/requests/{internal_request}/close', [RequestController::class, 'close'])
        ->name('requests.close');
});

// ==========================================
// AUTHENTIFICATION
// ==========================================

require __DIR__ . '/auth.php';

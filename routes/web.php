<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard.admin');
        })->name('dashboard');

        // Employees
        Route::resource('employees', EmployeeController::class);
    });

// ==========================================
// MANAGER ROUTES
// ==========================================
Route::middleware(['auth', 'active', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Manager Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard.manager');
        })->name('dashboard');
    });

// ==========================================
// EMPLOYEE ROUTES
// ==========================================
Route::middleware(['auth', 'active', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        // Employee Dashboard
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
// HOMEPAGE
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// GENERAL DASHBOARD REDIRECTION
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
// PROFILE ROUTES
// ==========================================
Route::middleware(['auth', 'active'])->group(function () {

    // Display profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    // Update name, email and phone
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Update password
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
// AUTHENTICATION ROUTES
// ==========================================
require __DIR__.'/auth.php';

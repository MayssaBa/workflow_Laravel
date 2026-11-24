<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;

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

    // Delete account
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


// ==========================================
// AUTHENTICATION ROUTES
// ==========================================
require __DIR__ . '/auth.php';

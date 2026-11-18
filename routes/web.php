<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Admin dashboard
Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.admin');
        })->name('dashboard');
    });

// Manager dashboard
Route::middleware(['auth', 'active', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.manager');
        })->name('dashboard');
    });

// Employee dashboard
Route::middleware(['auth', 'active', 'role:employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard.employee');
        })->name('dashboard');
    });

// Homepage: redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

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
    })
    ->name('dashboard');

// Profile routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';

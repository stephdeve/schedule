<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminController;

Route::get('/', fn () => view('welcome'));

// Auth web (session)

Route::get('/setup', [WebAuthController::class, 'showSetupForm'])->name('setup');
Route::post('/setup', [WebAuthController::class, 'setupAdmin'])->name('setup.submit');
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Admin Dashboard (Livewire si présent, sinon fallback Blade)
if (class_exists(\Livewire\Livewire::class)) {
    Route::get('/admin', \App\Livewire\Admin\Dashboard::class)
        ->middleware(['auth', 'role:admin'])
        ->name('admin.dashboard');
} else {
    Route::get('/admin', [AdminController::class, 'index'])
        ->middleware(['auth', 'role:admin'])
        ->name('admin.dashboard');
}
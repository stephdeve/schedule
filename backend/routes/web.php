<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Dashboard (Livewire v3 component, accessible une fois Livewire installé)
if (class_exists('Livewire\\Livewire', false)) {
    Route::get('/admin', \App\Livewire\Admin\Dashboard::class)
        ->middleware(['role:admin'])
        ->name('admin.dashboard');
} else {
    Route::get('/admin', function () {
        return 'Dashboard Livewire indisponible (package non installé)';
    })->middleware(['role:admin'])->name('admin.dashboard');
}

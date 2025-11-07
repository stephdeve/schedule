<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\EmploiController;
use App\Http\Controllers\NotificationController;

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Logout sous auth uniquement
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Admin: nécessite auth + rôle admin
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);

    // Users CRUD (admin uniquement)
    Route::apiResource('users', UserController::class);

    // Cours CRUD + publication (admin)
    Route::apiResource('cours', CoursController::class);
    Route::post('cours/{id}/publish', [CoursController::class, 'publish']);

    // Emplois: publier un emploi du temps (liste de cours)
    Route::post('/emplois/publier', [EmploiController::class, 'publish']);
});

// Emploi du temps
Route::get('/emplois', [EmploiController::class, 'index']);
Route::get('/emplois/{user_id}', [EmploiController::class, 'show']);

// Notifications utilisateur
Route::get('/notifications/{user_id}', [NotificationController::class, 'indexByUser']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
});

Route::middleware('auth')->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
    Route::patch('/users/me', [UserController::class, 'updateMe']);
    Route::delete('/users/me', [UserController::class, 'deleteMe']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::patch('/users/{userId}/role', [UserController::class, 'updateRole']);
});

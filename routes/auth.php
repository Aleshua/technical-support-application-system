<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'registerForm']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'loginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'confirmEmail'])
        ->middleware('signed')->
        name('verification.verify');

    Route::post('/email/resend', [AuthController::class, 'resendEmailVerification'])
        ->name('verification.resend');
});

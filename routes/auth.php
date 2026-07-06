<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    // Password reset routes
    Route::get('forgot-password', [PasswordResetController::class, 'request'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])
        ->name('password.reset');

    Route::post('reset-password', [PasswordResetController::class, 'update'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

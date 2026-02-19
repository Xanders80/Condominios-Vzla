<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\VerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordConfirmationController;
use Illuminate\Support\Facades\Route;

/**
 * API RESTful Routes for Authentication
 * 
 * All routes are prefixed with /api/v1/auth
 */

Route::prefix('v1/auth')->group(function () {
    
    // Public routes
    Route::post('/register', [AuthController::class, 'register'])
        ->name('api.auth.register');
    
    Route::post('/login', [AuthController::class, 'login'])
        ->name('api.auth.login');
    
    // Password reset routes
    Route::prefix('password')->group(function () {
        Route::post('/email', [PasswordController::class, 'sendResetLink'])
            ->name('api.password.email');
        
        Route::put('/reset', [PasswordController::class, 'reset'])
            ->name('api.password.reset');
    });
    
    // Email verification routes
    Route::prefix('email')->group(function () {
        Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->middleware(['auth:sanctum', 'signed'])
            ->name('api.verification.verify');
        
        Route::post('/resend', [VerificationController::class, 'resend'])
            ->middleware(['auth:sanctum', 'throttle:6,1'])
            ->name('api.verification.send');
    });
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.auth.logout');
        
        Route::get('/user', [AuthController::class, 'user'])
            ->name('api.auth.user');
        
        Route::post('/confirm-password', [PasswordConfirmationController::class, 'confirm'])
            ->name('api.auth.confirm-password');
    });
});

<?php

use App\Http\Controllers\jsController;
use App\Http\Controllers\Backend\Auth\AuthController;
use App\Http\Controllers\Backend\Auth\PasswordResetController;
use App\Http\Controllers\Backend\Auth\NewPasswordController;
use App\Http\Controllers\Backend\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Backend\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Backend\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('js')->as('js')->group(function () {
    Route::any('/{layout}/{page}/{file}', [jsController::class, 'javaScript']);
});
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('login', [AuthController::class, 'formLogin'])->name('login');
Route::get('terms-of-use', [AuthController::class, 'termsofuse'])->name('terms-of-use');
Route::get('register', [AuthController::class, 'formRegister'])->name('register');
Route::post('sign-in', [AuthController::class, 'login'])->name('sign-in');
Route::post('sign-up', [AuthController::class, 'register'])->name('sign-up');

Route::get('forgot-password', [PasswordResetController::class, 'showResetLinkEmail'])->name('link.reset');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('link.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'showResetPassword'])->name('password.reset');
Route::post('reset-password/store', [NewPasswordController::class, 'storeResetPassword'])->name('password.store');

Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'resendVerificationEmail'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
});
		//{{route replacer}} DON'T REMOVE THIS LINE

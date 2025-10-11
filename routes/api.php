<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialMediaAuthController;
use App\Http\Controllers\Settings\SelectRoleController;
use App\Http\Middleware\HasRoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('otp-login', [AuthController::class, 'otpLogin'])->name('otpLogin');
    Route::get('{provider}/redirect', [SocialMediaAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('{provider}/callback', [SocialMediaAuthController::class, 'callback'])->name('social.callback');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('email/verify', [AuthController::class, 'verifyEmail'])->name('email.verify');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('resend-verification-email/{user}', [AuthController::class, 'resendVerificationEmail'])->name('resendVerificationEmail');
    Route::post('select-role/{user}', [SelectRoleController::class, 'store'])->name('selectRole');

    Route::middleware([HasRoleMiddleware::class, 'verified'])->group(function () {

    });

});
<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialMediaAuthController;
use App\Http\Middleware\HasRoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
    Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('api.sendOtp');
    Route::post('otp-login', [AuthController::class, 'otpLogin'])->name('api.otpLogin');
    Route::get('{provider}/redirect', [SocialMediaAuthController::class, 'redirect'])->name('api.social.redirect');
    Route::get('{provider}/callback', [SocialMediaAuthController::class, 'callback'])->name('api.social.callback');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('email/verify', [AuthController::class, 'verifyEmail'])->name('api.email.verify');
    Route::post('logout', [AuthController::class, 'logout'])->name('api.logout');

      Route::middleware([HasRoleMiddleware::class, 'verified'])->group(function () {

    });

});

<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialMediaAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('otp-login', [AuthController::class, 'otpLogin']);
    Route::post('facebook', [SocialMediaAuthController::class, 'facebookAuth']);
    Route::post('google', [SocialMediaAuthController::class, 'googleAuth']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});
<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialMediaAuthController;
use App\Http\Middleware\HasRoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('otp-login', [AuthController::class, 'otpLogin']);
    Route::get('{provider}/redirect', [SocialMediaAuthController::class, 'redirect']);
    Route::get('{provider}/callback', [SocialMediaAuthController::class, 'callback']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware([HasRoleMiddleware::class, 'verified'])->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        });
    });

});

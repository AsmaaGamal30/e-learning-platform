<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialMediaAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->withoutMiddleware('guest')->middleware('auth:sanctum');
    Route::post('facebook', [SocialMediaAuthController::class, 'facebookAuth']);
    Route::post('google', [SocialMediaAuthController::class, 'googleAuth']);
});

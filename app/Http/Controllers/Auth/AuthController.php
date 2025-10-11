<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginWithOtpRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function otpLogin(LoginWithOtpRequest $request)
    {
        return $this->authService->loginWithOtp($request->validated());
    }

    public function sendOtp(Request $request)
    {
        return $this->authService->sendOtp($request->only('email'));
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        return $this->authService->verifyEmail($request->only('code'));
    }

    public function resendVerificationEmail(User $user)
    {
        return $this->authService->resendVerificationEmail($user);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}

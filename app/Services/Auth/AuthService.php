<?php

namespace App\Services\Auth;

use App\Enums\Role;
use App\Events\OtpLogin;
use App\Events\Registered;
use App\Mail\OtpLoginEmail;
use App\Mail\VerifyEmailUsingCode;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function login(array $data)
    {
        $user = User::where("email", $data["email"])->first();
        if ($user && password_verify($data["password"], $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'access_token' => $token,
                'user' => $user->load('media'),
            ];
        }
        return null;
    }

    public function loginWithOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $cachedOtp = Cache::get("otp_{$user->id}");

        if (!$cachedOtp) {
            return response()->json(['message' => 'OTP expired'], Response::HTTP_UNAUTHORIZED);
        }

        if ($cachedOtp !== (int) $data['otp']) {
            return response()->json(['message' => 'Invalid OTP'], Response::HTTP_UNAUTHORIZED);
        }

        Cache::forget("otp_{$user->id}");

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'user' => $user->load('media'),
        ];
    }

    public function sendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $cachedOtp = Cache::get("otp_{$user->id}");

            if ($cachedOtp) {
                Cache::forget("otp_{$user->id}");
            }

            $otp = rand(100000, 999999);
            Cache::put("otp_{$user->id}", $otp, now()->addMinutes(10));

            event(new OtpLogin($user, $otp));

            return true;
        }
        return false;
    }

    public function register(array $data)
    {
        $user = User::create([
            "name" => $data['name'],
            "email" => $data['email'],
            "phone" => $data['phone'],
            "password" => bcrypt($data['password']),
        ]);

        if (isset($data['image'])) {

            $path = Storage::disk('public')->put('avatar', $data['image']);
            $user->media()->create([
                'url' => Storage::url($path),
                'type' => pathinfo($path, PATHINFO_EXTENSION),
            ]);
        }

        if ($data['is_teacher'] == true) {

            $role = Role::TEACHER;

            $team = Team::create([
                'name' => $data['name'] . "'s Team",
                'user_id' => $user->id,
                'personal_team' => true,
            ]);

            $user->current_team_id = $team->id;
            $user->save();
            $team->users()->attach($user->id, ['role' => $role->value]);

        } else {
            $role = Role::STUDENT;

            $team = Team::create([
                'name' => $data['name'] . "'s Study Group",
                'user_id' => $user->id,
                'personal_team' => false,
            ]);

            $user->current_team_id = $team->id;
            $user->save();
            $team->users()->attach($user->id, ['role' => $role->value]);

        }

        event(new Registered($user));

        return $user;

    }

    public function resendVerificationEmail(User $user)
    {

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], Response::HTTP_BAD_REQUEST);
        }

        $user->verification_code = rand(100000, 999999);
        $user->save();

        event(new Registered($user));

        return response()->json(['message' => 'Verification email resent'], Response::HTTP_OK);
    }

    public function verifyEmail(array $data)
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], Response::HTTP_BAD_REQUEST);
        }

        if ($user->verification_code !== $data['code']) {
            return response()->json(['message' => 'Invalid verification code'], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->verification_code === $data['code']) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return response()->json(['message' => 'Email verified successfully'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Email verification failed'], Response::HTTP_UNAUTHORIZED);

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
    }
}

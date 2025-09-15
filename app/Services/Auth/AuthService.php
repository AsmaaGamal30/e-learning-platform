<?php

namespace App\Services\Auth;

use App\Enums\Role;
use App\Mail\OtpLoginEmail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
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
                'user' => $user,
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

        if ($cachedOtp !== $data['otp']) {
            return response()->json(['message' => 'Invalid OTP'], Response::HTTP_UNAUTHORIZED);
        }

        Cache::forget("otp_{$user->id}");

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'user' => $user,
        ];
    }

    public function sendOtp(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->save();
            Cache::put("otp_{$user->id}", $otp, now()->addMinutes(10));

            Mail::to($user->email)->send(new OtpLoginEmail($user->name, $otp));

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
            "is_teacher" => $data['is_teacher'],
            "image" => isset($data['image']) ? $data['image']->store('profile-images', 'public') : null,
        ]);

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

        return $user;

    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
    }
}

<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialMediaAuthService
{

    public function redirect(string $provider)
    {
        if (in_array($provider, ['google', 'facebook'])) {
            return Socialite::driver($provider)->stateless()->redirect();
        }

        abort(Response::HTTP_NOT_FOUND, 'Unsupported provider');
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(Response::HTTP_NOT_FOUND, 'Unsupported provider');
        }

        $socialUser = Socialite::driver($provider)->stateless()->user();


        $user = User::updateOrCreate(
            ['email' => $socialUser->email],
            [
                'name' => $socialUser->name,
                'auth_provider_id' => $socialUser->id,
                'auth_provider' => $provider,
                'auth_provider_token' => $socialUser->token,
                'auth_provider_refresh_token' => $socialUser->refreshToken ?? null,
                'email_verified_at' => now(),
            ]
        );

        if ($socialUser->avatar) {
            try {
                $contents = file_get_contents($socialUser->avatar);
                $extension = pathinfo(parse_url($socialUser->avatar, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = 'avatars/' . uniqid() . '.' . $extension;

                Storage::disk('public')->put($filename, $contents);

                $user->media()->create([
                    'url' => Storage::url($filename),
                    'type' => $extension,
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to save avatar from $provider", [
                    'error' => $e->getMessage(),
                    'avatar_url' => $socialUser->avatar,
                ]);
            }
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user->load('media'),
        ]);
    }


}

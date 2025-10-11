<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            "email" => "user@example.com",
            "password" => Hash::make("password"),
        ]);
    }

    #[Test]
    public function user_can_login_with_email_and_password()
    {
        $response = $this->postJson(route('login'), [
            'email' => "user@example.com",
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_login_with_otp()
    {

        $otp = random_int(100000, 999999);
        Cache::put("otp_{$this->user->id}", $otp, now()->addMinutes(10));

        $response = $this->postJson(route('otpLogin'), [
            'email' => "user@example.com",
            'otp' => (string) $otp,
        ]);

        $response->assertStatus(200);
    }
}

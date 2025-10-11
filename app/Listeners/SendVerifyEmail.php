<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Mail\VerifyEmailUsingCode;
use Illuminate\Support\Facades\Mail;

class SendVerifyEmail
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (empty($user->auth_provider)) {
            $this->sendVerificationEmail($user);
        }
    }

    protected function sendVerificationEmail($user): void
    {
        $verificationCode = $this->generateVerificationCode($user);
        Mail::to($user->email)->send(new VerifyEmailUsingCode($user->name, $verificationCode));
    }

    protected function generateVerificationCode($user): string
    {
        $verificationCode = random_int(100000, 999999);
        $user->verification_code = $verificationCode;
        $user->save();

        return (string) $verificationCode;
    }
}

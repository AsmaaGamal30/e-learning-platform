<?php

namespace App\Listeners;

use App\Events\OtpLogin;
use App\Mail\OtpLoginEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOtp
{

    /**
     * Handle the event.
     */
    public function handle(OtpLogin $event): void
    {
        $user = $event->user;
        $otp = $event->otp;

        Mail::to($user->email)->send(new OtpLoginEmail($user->name, $otp));
    }
}
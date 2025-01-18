<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\Otp as MailOtp;


class EmailService
{
    public function sendOtp($email, $name, $otp)
    {
        return Mail::to($email)->send(
            new MailOtp($name, $otp)
        );
    }

}

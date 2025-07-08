<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $name;

    public function __construct($otpCode, $name)
    {
        $this->otpCode = $otpCode;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Kode OTP Anda')
            ->view('otp') 
            ->with([
                'otpCode' => $this->otpCode,
                'name' => $this->name,
            ]);
    }
}

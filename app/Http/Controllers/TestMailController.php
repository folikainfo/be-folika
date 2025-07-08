<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    public function send()
    {
        Mail::raw('Kami dari folika izin mengirimkan otp', function ($msg) {
            $msg->to('farinanaswa@gmail.com')  // Ganti dengan email tujuan
                ->subject('Tes SMTP');
        });

        return 'Email terkirim!';
    }
}

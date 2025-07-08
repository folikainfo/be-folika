<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Carbon\Carbon;
use App\Models\User;


class OtpController extends Controller
{
    // Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.email' => 'Email harus valid',
            'email.required' => 'Email tidak boleh kosong',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email sudah terdaftar. Silakan masuk.',
            ], 409); // 409 Conflict
        }

        $now = Carbon::now();

        // Ambil OTP terakhir yang belum digunakan
        $existingOtp = Otp::where('email', $request->email)
            ->where('is_used', false)
            ->orderByDesc('created_at')
            ->first();

        if ($existingOtp && $existingOtp->expires_at > $now) {
            $lastSent = $existingOtp->created_at;
            $canResendAt = $lastSent->addMinutes(1);

            if ($now < $canResendAt) {
                $remainingSeconds = $now->diffInSeconds($canResendAt);
                $minutes = floor($remainingSeconds / 60);
                $seconds = $remainingSeconds % 60;

                return response()->json([
                    'message' => "OTP sudah dikirim. Anda dapat meminta ulang dalam {$minutes} menit {$seconds} detik.",
                ], 429); // Too Many Requests
            }

            // Jika bisa resend, expired-kan OTP lama
            $existingOtp->update(['expires_at' => $now]);
        }

        // Buat OTP baru
        $otpCode = rand(100000, 999999);

        Otp::create([
            'email' => $request->email,
            'code' => $otpCode,
            'expires_at' => $now->copy()->addMinutes(5),
            'is_used' => false,
        ], );

        $name = $request->name ?? explode('@', $request->email)[0];
        Mail::to($request->email)->send(new OtpMail($otpCode, $name));

        return response()->json(['message' => 'Kode OTP telah dikirim ke email']);
    }

    // Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ], [
            'code.required' => "Kode OTP tidak boleh kosong"
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Kode OTP tidak valid atau telah kadaluarsa'], 400);
        }

        // Tandai OTP sudah digunakan
        $otp->update(['is_used' => true]);

        // Kamu bisa update status user di sini jika perlu

        return response()->json(['message' => 'OTP berhasil diverifikasi']);
    }
}

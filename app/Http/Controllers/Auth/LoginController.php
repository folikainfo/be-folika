<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email Tidak Boleh Kosong',
            'email.email' => 'Email harus valid',
            'password.required' => 'Kata Sandi Tidak Boleh Kosong',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek user dan password
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau kata sandi salah'], 401);
        }

        // Buat token
        $token = $user->createToken('apifolika')->plainTextToken;

        return response()->json([
            'user'   => $user,
            'token'  => $token,
            'message' => 'Login berhasil',
        ]);
    }
}

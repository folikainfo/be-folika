<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    // Tentukan atribut yang dapat diisi secara mass-assignment
    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'is_used',
    ];

    // Tentukan atribut yang sebaiknya disembunyikan saat serialisasi
    protected $hidden = [
        'code', // Jika tidak ingin mengirimkan kode OTP melalui API
    ];

    // Tentukan format casting untuk atribut tertentu
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}

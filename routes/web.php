<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestMailController;  // Pastikan 'use' di sini

Route::get('/', function () {
    return view('welcome');
});

// Route yang dipanggil menggunakan controller yang benar
Route::get('/test-email', [TestMailController::class, 'send']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PredictController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', RegisterController::class);
Route::post('/login', LoginController::class);
Route::post('/otp/send', [OtpController::class, 'sendOtp']);
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/user/photo', [UserController::class, 'updateProfilePhoto']);
    Route::post('/user', [UserController::class, 'update']);
    Route::post('/user/changePassword', [UserController::class, 'changePassword']);
    Route::post('/predict', [PredictController::class, 'store']);
    Route::get('/histories', [PredictController::class, 'index']);
    Route::get('/latest-history', [PredictController::class, 'latestHistory']);


});

Route::any('/check-method', function (Request $request) {
    return response()->json([
        'actual_method' => $request->method(),
        'all_input'     => $request->all(),
    ]);
});

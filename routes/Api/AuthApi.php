<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('logout', [AuthenticationController::class, 'logout']);
    Route::get('authenticate', [AuthenticationController::class, 'authenticate']);
    Route::post('send-token', [AuthenticationController::class, 'send_token_sign_up']);
    Route::post('sign-up', [AuthenticationController::class, 'validate_mail']);
});

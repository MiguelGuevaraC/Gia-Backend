<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Routing\Route;

Route::post('send-token', [AuthenticationController::class, 'send_token_sign_up']);
Route::post('sign-up', [AuthenticationController::class, 'validate_mail']);

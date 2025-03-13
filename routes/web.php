<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('login');
Route::get('view_token_email', [UserController::class, 'view_token_email']);
Route::post('send-token', [AuthenticationController::class, 'send_token_sign_up']);
Route::post('sign-up', [AuthenticationController::class, 'validate_mail']);
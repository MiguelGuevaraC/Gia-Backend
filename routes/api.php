<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthenticationController::class, 'login']);

Route::group(["middleware" => ["auth:sanctum"]], function () {

    require __DIR__ . '/Api/AuthApi.php';//AUTHENTICATE
    require __DIR__ . '/Api/SearchApi.php';// SEARCH
    require __DIR__ . '/Api/CompanyApi.php'; //CLIENTS
    require __DIR__ . '/Api/PersonApi.php'; //PERSON
    require __DIR__ . '/Api/UserApi.php'; //USER
    require __DIR__ . '/Api/RolApi.php'; //ROL
});

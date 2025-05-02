<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('setting', [SettingController::class, 'index']);
    Route::put('update-time-reservation', [SettingController::class, 'update_time_reservation']);
    Route::put('update-descount-percent', [SettingController::class, 'update_descount_percent']);
});

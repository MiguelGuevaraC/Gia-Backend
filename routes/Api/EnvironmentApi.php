<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EnvironmentController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('environment', [EnvironmentController::class, 'index']);
    Route::post('environment', [EnvironmentController::class, 'store']);
    Route::get('environment/{id}', [EnvironmentController::class, 'show']);
    Route::post('environment/{id}', [EnvironmentController::class, 'update']);
    Route::delete('environment/{id}', [EnvironmentController::class, 'destroy']);

});

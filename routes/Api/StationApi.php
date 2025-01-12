<?php

use App\Http\Controllers\StationController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('station', [StationController::class, 'index']);
    Route::post('station', [StationController::class, 'store']);
    Route::get('station/{id}', [StationController::class, 'show']);
    Route::post('station/{id}', [StationController::class, 'update']);
    Route::delete('station/{id}', [StationController::class, 'destroy']);

});

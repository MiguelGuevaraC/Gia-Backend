<?php

use App\Http\Controllers\RerservationController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('reservation', [RerservationController::class, 'index']);
    Route::post('reservation', [RerservationController::class, 'store']);
    Route::get('reservation/{id}', [RerservationController::class, 'show']);
    Route::put('reservation/{id}', [RerservationController::class, 'update']);
    Route::delete('reservation/{id}', [RerservationController::class, 'destroy']);

});

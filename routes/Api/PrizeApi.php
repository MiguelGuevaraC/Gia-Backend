<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PrizeController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('event', [PrizeController::class, 'index']);
    Route::post('event', [PrizeController::class, 'store']);
    Route::get('event/{id}', [PrizeController::class, 'show']);
    Route::post('event/{id}', [PrizeController::class, 'update']);
    Route::delete('event/{id}', [PrizeController::class, 'destroy']);

});

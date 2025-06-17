<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PrizeController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    // Route::get('prize', [PrizeController::class, 'index']);
    // Route::post('prize', [PrizeController::class, 'store']);
    // Route::get('prize/{id}', [PrizeController::class, 'show']);
    // Route::post('prize/{id}', [PrizeController::class, 'update']);
    // Route::delete('prize/{id}', [PrizeController::class, 'destroy']);

});

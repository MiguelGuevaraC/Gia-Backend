<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('event', [EventController::class, 'index']);
    Route::post('event', [EventController::class, 'store']);
    Route::get('event/{id}', [EventController::class, 'show']);
    Route::post('event/{id}', [EventController::class, 'update']);
    Route::delete('event/{id}', [EventController::class, 'destroy']);

});

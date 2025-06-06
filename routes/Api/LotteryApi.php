<?php

use App\Http\Controllers\LotteryController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('lottery', [LotteryController::class, 'index']);
    Route::post('lottery', [LotteryController::class, 'store']);
    Route::get('lottery/{id}', [LotteryController::class, 'show']);
    Route::put('lottery/{id}', [LotteryController::class, 'update']);
    Route::delete('lottery/{id}', [LotteryController::class, 'destroy']);

});

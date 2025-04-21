<?php

use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('promotion', [PromotionController::class, 'index']);
    Route::get('promotion-app', [PromotionController::class, 'index_app']);

    Route::post('promotion', [PromotionController::class, 'store']);
    Route::get('promotion/{id}', [PromotionController::class, 'show']);
    Route::post('promotion/{id}', [PromotionController::class, 'update']);
    Route::delete('promotion/{id}', [PromotionController::class, 'destroy']);

});

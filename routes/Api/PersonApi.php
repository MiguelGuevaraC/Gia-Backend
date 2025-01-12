<?php

use App\Http\Controllers\PersonaController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('person', [PersonaController::class, 'index']);
    Route::post('person', [PersonaController::class, 'store']);
    Route::get('person/{id}', [PersonaController::class, 'show']);
    Route::put('person/{id}', [PersonaController::class, 'update']);
    Route::delete('person/{id}', [PersonaController::class, 'destroy']);

});

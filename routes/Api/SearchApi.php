<?php

use App\Http\Controllers\ApiExternaController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('searchByDni/{dni}', [ApiExternaController::class, 'searchByDni']);
    Route::get('searchByRuc/{ruc}', [ApiExternaController::class, 'searchByRuc']);
});

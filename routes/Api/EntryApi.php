<?php

use App\Http\Controllers\EntryController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('entry', [EntryController::class, 'index']);
    Route::post('entry', [EntryController::class, 'store']);
    Route::get('entry/{id}', [EntryController::class, 'show']);
    Route::post('entry/{id}', [EntryController::class, 'update']);
    Route::delete('entry/{id}', [EntryController::class, 'destroy']);

});

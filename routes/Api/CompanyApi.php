<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('company', [CompanyController::class, 'index']);
    Route::get('company-list', [CompanyController::class, 'list']);

    Route::post('company', [CompanyController::class, 'store']);
    Route::get('company/{id}', [CompanyController::class, 'show']);
    Route::post('company/{id}', [CompanyController::class, 'update']);
    Route::delete('company/{id}', [CompanyController::class, 'destroy']);

});

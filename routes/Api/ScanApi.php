<?php

use App\Http\Controllers\CodeGeneratorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
  //  Route::post('scanner', [CodeGeneratorController::class, 'scanner']);

});

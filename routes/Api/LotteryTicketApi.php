<?php

use App\Http\Controllers\LotteryTicketController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('lottery_ticket', [LotteryTicketController::class, 'index']);
    Route::post('lottery_ticket', [LotteryTicketController::class, 'store']);
    Route::post('lottery_ticket_admin', [LotteryTicketController::class, 'store_admin']);
    Route::get('lottery_ticket/{id}', [LotteryTicketController::class, 'show']);
    Route::put('lottery_ticket/{id}', [LotteryTicketController::class, 'update']);
    Route::delete('lottery_ticket/{id}', [LotteryTicketController::class, 'destroy']);
 Route::get('lotteryHistory', [LotteryTicketController::class, 'lotteryHistory']);
});

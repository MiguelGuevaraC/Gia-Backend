<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthenticationController::class, 'login_admin']);

Route::post('login-app', [AuthenticationController::class, 'login_app']);
// Route::post('login-admin', [AuthenticationController::class, 'login_admin']);

Route::group(["middleware" => ["auth:sanctum"]], function () {

    require __DIR__ . '/Api/AuthApi.php';        //AUTHENTICATE
    require __DIR__ . '/Api/SearchApi.php';      // SEARCH
    require __DIR__ . '/Api/CompanyApi.php';     //CLIENTS
    require __DIR__ . '/Api/PersonApi.php';      //PERSON
    require __DIR__ . '/Api/UserApi.php';        //USER
    require __DIR__ . '/Api/RolApi.php';         //ROL
    require __DIR__ . '/Api/EnvironmentApi.php'; //ENVIRONMENT
    require __DIR__ . '/Api/StationApi.php';     //ROL
    require __DIR__ . '/Api/PermissionApi.php';  //PERMISSIONS
    require __DIR__ . '/Api/EventApi.php';       //EVENTS
    require __DIR__ . '/Api/ReservationApi.php'; //RESERVATIONS
    require __DIR__ . '/Api/EntryApi.php';       //ENTRY
    require __DIR__ . '/Api/ProductApi.php';     //PRODUCT
    require __DIR__ . '/Api/PromotionApi.php';   //PROMOTION
    require __DIR__ . '/Api/SettingApi.php';     //SETTING
    require __DIR__ . '/Api/GalleryApi.php';     //GALLERY
    require __DIR__ . '/Api/LotteryApi.php';     //LOTTERY
    require __DIR__ . '/Api/LotteryTicketApi.php';     //LOTTERY TICKET
    require __DIR__ . '/Api/PrizeApi.php';     //LOTTERY TICKET
});

Route::post('send-token', [AuthenticationController::class, 'send_token_sign_up']);
Route::post('sign-up', [AuthenticationController::class, 'validate_mail']);

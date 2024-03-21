<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PayElectricityController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\CableTVController;
use App\Http\Controllers\BuyDataController;
use App\Http\Controllers\SavedCardsController;
use App\Http\Controllers\SavedMeterController;
use App\Http\Controllers\PaymentController;
use Questocat\Referral\Traits\UserReferral;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/register',             [AuthController::class, 'register']);
Route::post('/login',             [AuthController::class, 'login']);
Route::get('/login/facebook/callback',      [AuthController::class, 'facebookCallback']);
Route::get('/login/google/callback',        [AuthController::class, 'googleCallback']);


//user Routes
Route::middleware(['auth:sanctum'])->group(function(){   
    //show user profile
    Route::get('/user',            [UsersController::class, 'viewProfile']);
    Route::put('/user',            [UsersController::class, 'updateProfile']);
    Route::get('/points-to-cash',            [UsersController::class, 'pointsToCash']);

    Route::get('/wallet/balance',            [WalletController::class, 'balance']);
    Route::get('/transaction/history',            [WalletController::class, 'transactionHistory']);

    Route::get('/payment/history',            [PaymentController::class, 'paymentHistory']);

    
    //get user referral link
    Route::get('/referral-link',            [UsersController::class, 'referralCode'])->middleware('referral');

    //Electricity Routes
    Route::get('/getdisco',             [PayElectricityController::class, 'getAvailableDisco']);
    Route::get('/power/info',             [PayElectricityController::class, 'getMeterInfo']);
    Route::post('/power/buy',             [PayElectricityController::class, 'vendPower']);



    //buy Airtime
    Route::post('/vendairtime',             [AirtimeController::class, 'buyAirtime']);



    //cable Tv route
    Route::post('/tv/plans',             [CableTVController::class, 'getTV']);

    Route::get('/tv/info',             [CableTVController::class, 'getTvInfo']);
    // Route::get('/tv/startimes/info',             [CableTVController::class, 'getStarTimesTvInfo']);

    Route::post('/vendtv',              [CableTVController::class, 'vendTv']);

    // Route::get('/startimes/vend',              [CableTVController::class, 'vendStarTimes']);



    //buy data
    Route::get('/data/bundles',              [BuyDataController::class, 'getBundles']);
    Route::post('/data/purchase',              [BuyDataController::class, 'buyData']);

    //check service status
    Route::post('/check/status',              [BuyDataController::class, 'viewStatus']);

    //save new user card information
    Route::post('/cards',               [SavedCardsController::class, 'newCard']);
    Route::get('/cards/{id}',               [SavedCardsController::class, 'showCard']);
    Route::get('/all/cards',               [SavedCardsController::class, 'allCards']);
    Route::put('/cards/{id}',               [SavedCardsController::class, 'updateCard']);
    Route::delete('/cards/{id}',               [SavedCardsController::class, 'deleteCard']); 

    //save Meter information
    Route::post('/meters',               [SavedMeterController::class, 'newMeter']);
    Route::get('/meters/{id}',               [SavedMeterController::class, 'showMeter']);
    Route::get('/all/meters',               [SavedMeterController::class, 'allMeter']);
    Route::put('/meters/{id}',               [SavedMeterController::class, 'updateMeter']);
    Route::delete('/meters/{id}',               [SavedMeterController::class, 'deleteMeter']); 


    //fund wallet...paystack payment callback
    Route::get('/payment/callback', [PaymentController::class,'handleGatewayCallback']);

    //logout
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::fallback(function(){
        return response()->json([
            'message' => 'Resource Not Found'
        ], 404);
    });

});

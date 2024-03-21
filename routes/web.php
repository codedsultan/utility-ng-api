<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login/facebook',               [AuthController::class, 'facebookRedirect']);
Route::get('/login/google',                 [AuthController::class, 'googleRedirect']);
// Route::post('/pay',             [PaymentController::class,'redirectToGateway'])->name('pay');


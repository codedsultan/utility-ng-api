<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Http\Controllers\PayElectricityController;
use App\Http\Requests\PurchaseAirtimeRequest;
use App\Services\WalletService;

class AirtimeController extends Controller
{
    //buy airtime
     public function buyAirtime(PurchaseAirtimeRequest $request){

        try{

            $request->validated();
            $walletService = new WalletService(auth()->user()->id);
            return $walletService->purchaseAirtime($request->vtu_number, $request->vtu_network, $request->vtu_amount);
        }catch(\Throwable $th){
            throw $th;
        }

    }
        
}

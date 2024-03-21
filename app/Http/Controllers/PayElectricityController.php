<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Http\Requests\MeterInfoRequest;
use App\Http\Requests\VendPowerRequest;
use App\Services\IrechargeService;
use App\Services\WalletService;

class PayElectricityController extends Controller
{
    //----------ELECTRICITY------
    public function getAvailableDisco(){
        try{
            $irechargeService = new IrechargeService();
            return $irechargeService->getDisco();
        }catch(\Throwable $th){
            throw $th;
        }

    }

    
        //get meter information, generate reference id and access token
    public function getMeterInfo(MeterInfoRequest $request){
            $request->validated();
            try{
                $data = [
                    'meter_number' => $request->meter_number,
                    'disco' => $request->disco 
                ];

                $irechargeService = new IrechargeService();
                return $irechargeService->getMeterInfo($data);
                 
            }catch(\Throwable $th){
               throw $th;
            }
           
   
    }
   

//buy electricity 
    public function vendPower(VendPowerRequest $request){
        try{
            $request->validated();
            $data = [   
                'meter_number' => $request->meter_number,
                'disco' => $request->disco,
                'amount' => $request->amount,
                'access_token' => $request->access_token,
            ];
            //check user wallet if it has sufficient funds

            $wallet = new WalletService(auth()->user()->id);
            return $wallet->purchasePower($data);
        }catch(\Throwable $th){
            throw $th;
        }
         
    }

}

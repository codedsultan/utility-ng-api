<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Services\IrechargeService;
use App\Services\WalletService;
use App\Http\Requests\DataBundleRequest;
use App\Http\Requests\VendStatusRequest;
use App\Http\Requests\BuyDataBundleRequest;


class BuyDataController extends Controller
{
    //get data bundles
    public function getBundles(DataBundleRequest $request, IrechargeService $irechargeService){

        try{

            $request->validated();
            return $irechargeService->dataBundles($request->data_network);
        
        }catch(\Throwable $th){
            throw $th;
        }
    }

    //buy data
    public function buyData(BuyDataBundleRequest $request){
        try{

         $request->validated();

            $vtu_network = $request->vtu_network;
            $vtu_number = $request->vtu_number;
            $vtu_data = $request->vtu_data;
        
        $wallet = new WalletService(auth()->user()->id);
        return $wallet->purchaseData($vtu_number, $vtu_network, $vtu_data);
    
        }catch(\Throwable $th){
            throw $th;
        }
    }


    //view service status
    public function viewStatus(VendStatusRequest $request){
        try{

            $request->validated();

            $irechargeService = new IrechargeService();
            $service_type = $request->service_type;
            $reference_id = $request->reference;

            return $irechargeService->checkVendStatus($service_type, $reference_id);

        }catch(\Throwable $th){
            throw $th;
        }
    }
}

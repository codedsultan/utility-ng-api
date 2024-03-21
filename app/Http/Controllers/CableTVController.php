<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Services\IrechargeService;
use App\Services\WalletService;
use App\Http\Requests\getTvPlanRequest;
use App\Http\Requests\TvInfoRequest;
use App\Http\Requests\vendTvRequest;

class CableTVController extends Controller
{
    //get GoTv and DsTv plans
    public function getTv(getTvPlanRequest $request, IrechargeService $irechargeService){

        try {
             $request->validated();
            //DSTV, GOTV or StarTimes
            $tv_network = $request->tv_network;        
            return $irechargeService->getTvPlans($tv_network);

        } catch (\Throwable $th) {
            throw $th;
        }
         
    }


    //get gotv and dstv smartcard information only
    public function getTvInfo(TvInfoRequest $request, IrechargeService $irechargeService){

        $request->validated();
 
        $data = [
            'tv_network' => $request->tv_network,
            'service_code' => $request->service_code,
            'smartcardnumber' => $request->smartcard_number
        ];
      
        return $irechargeService->getTvInfo($data);

    }


    //subscribe to GOTV and DSTV
    public function vendTv(vendTvRequest $request, IrechargeService $irechargeService){
        
        try{
                $request->validated();
                $walletService = new WalletService(auth()->user()->id);
                $data = [
                    'smartcard_number' => $request->input('smartcard_number'),
                    'tv_network'       => $request->input('tv_network'),
                    'access_token'     => $request->input('access_token'),
                    'service_code'     => $request->input('service_code'),
                    'reference_id'     => $request->input('reference_id'),
                ];
               
                return $walletService->purchaseCableTv($data);
                

        }catch(\Throwable $th){
            throw $th;
        }

    }

}

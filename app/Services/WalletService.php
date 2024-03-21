<?php

namespace App\Services;

use App\Models\Wallet;
use App\Services\MyJsonResponseFormatter;
use App\Services\IrechargeService;
use App\Services\SaveMeterService;
use App\Models\Transaction;

class WalletService
{
    private $user_id;

    public function __construct($user_id){
        $this->user_id = $user_id;
    }

    
    //create new Wallet
    public function newWallet(){
        $wallet = Wallet::firstOrCreate([
            'user_id' => $this->user_id
        ]);
        return $wallet;
    }

    //get user wallet
    public function getWallet(){
       return Wallet::where('user_id', $this->user_id)->first();
    }

    //get wallet balance
    public function getBalance():float {
        $wallet = Self::getWallet();
        return (float)$wallet->balance;
    }

    //verify wallet balance againse purchase price
    public function verifyBalance($item_price):bool {
        $balance = Self::getBalance();
        if($balance > 0 && $balance > $item_price){
            return true;
        }
        return false;
    }

    public function debitWallet($item_price){
        $wallet = Self::getWallet();
        $wallet->balance -= $item_price;
        $wallet->save();
        return $wallet->balance;
    }

    //purchase airtime from wallet
    public function purchaseAirtime($vtu_number, $vtu_network, $vtu_amount){
        $irechargeService = new IrechargeService();
     
        //check user wallet if it has sufficient funds
        if(!Self::verifyBalance($vtu_amount)){
            return MyJsonResponseFormatter::messageResponse('Insufficient Balance', 400);
        }

        $irechargeService->airtime($vtu_number, $vtu_network, $vtu_amount);
        $url_response = $irechargeService->purchaseAirtime();
        $response = json_decode($url_response,true);
        $reference = $response['ref'];
        $status = $response['status'];
        //00 means successful 
        if($status == 00){
            //debit user wallet
            Self::debitWallet($vtu_amount);
            //store transaction
            $transaction = Transaction::create([
            'user_id' => auth()->user()->id,
            'phone_number' => $vtu_number,
            'amount' => $vtu_amount,
            'type' => 'Airtime Purchase',
            'vtu_network' => $vtu_network,
            'reference' => $reference
            ]);
        }
        return $response;
    }


    //purchase data
    public function purchaseData($vtu_number, $vtu_network, $vtu_data){
        $irechargeService = new IrechargeService();
        $irechargeService->data($vtu_number, $vtu_network, $vtu_data);
        $bundle_price = $irechargeService->getBundlePrice($vtu_network, $vtu_data);
         
        //check user wallet if it has sufficient funds
         if(!Self::verifyBalance((int)$bundle_price)){
            return MyJsonResponseFormatter::messageResponse('Insufficient Balance', 400);
        }

        $response = $irechargeService->purchaseData();
        //verify if the transaction is successful
        $status = $response['status'];
        $reference = $response['ref'];
        if($status == 00){    
            Self::debitWallet($bundle_price);
            $transaction = Transaction::create([
                    'user_id' => auth()->user()->id,
                    'phone_number' => $vtu_number,
                    'amount' => $bundle_price,
                    'type' => 'Data Top Up',
                    'vtu_data' => $vtu_data,
                    'vtu_network' => $vtu_network,
                    'reference' => $reference
                ]);
        }

        return $response;
    }


    //subscribe to tv
    public function purchaseCableTv($data){
       
        $irechargeService =  new IrechargeService();
        $irechargeService->cableTv($data['tv_network'], $data['smartcard_number'], $data['service_code'], $data['access_token']);
        $amount = $irechargeService->getTvPlanPrice($data['tv_network'], $data['service_code']);

           //check user wallet if it has sufficient funds
        if(!Self::verifyBalance($amount)){
            return MyJsonResponseFormatter::messageResponse('Insufficient Balance', 400);
        }

        $response = $irechargeService->vendTv($data);
        $status = $response['status'];
        
        if($status == 00){    
            Self::debitWallet($amount);
            //store transaction
            $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'phone_number' => '08023029102',
                'amount' => $amount,
                'type' => 'Cable TV Subscription',
                'smartcard_number' => $data['smartcard_number'],
                'tv_network' => $data['tv_network'],
                'reference' => $data['reference_id'],
            ]);
        }

        return $response;
    }


    //purchase Power
    public function purchasePower($data){
        if(!Self::verifyBalance($data['amount'])){
            return MyJsonResponseFormatter::messageResponse('Insufficient Balance', 400);
        }

        //connect to irecharge service
        $irechargeService = new IrechargeService();
        $irechargeService->electricity($data['meter_number'], $data['disco']);
        $response = $irechargeService->vendPower($data);
        $status = $response['status'];
        
        //verify irecharge response status
        if($status == 00){    
            Self::debitWallet($data['amount']);

            //store transaction
            $transaction = Transaction::create([
                'user_id' => auth()->user()->id,
                'phone_number' => '09840892839',
                'amount' => $data['amount'],
                'type' => 'Payment of Electricity Bill',
                'meter_type' => $data['disco'],
                'meter_number' => $data['meter_number'],
                'reference' => $response['ref']
            ]);

            //save meter information for future use
            $savedMeterService = new SaveMeterService();
            $savedMeterService->newMeter($data['meter_number'], $data['disco']);
        }

        return $response;

    }
}
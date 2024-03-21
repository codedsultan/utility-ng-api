<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\IrechargeHashService;

class IrechargeService
{   
    //named endpoints
    private $vendAirtimeUrl   = 'https://irecharge.com.ng/pwr_api_sandbox/v2/vend_airtime.php';
    private $getDataBundleUrl = 'https://irecharge.com.ng/pwr_api_sandbox/v2/get_data_bundles.php';
    private $buyDataBundleUrl = 'https://irecharge.com.ng/pwr_api_sandbox/v2/vend_data.php';
    private $vendStatusUrl    = 'https://irecharge.com.ng/pwr_api_sandbox/v2/vend_status.php';
    private $getTvPlansUrl    = 'https://irecharge.com.ng/pwr_api_sandbox/v2/get_tv_bouquet.php';
    private $getTvInfoUrl     = 'https://irecharge.com.ng/pwr_api_sandbox/v2/get_smartcard_info.php';
    private $vendTvUrl        = 'https://irecharge.com.ng/pwr_api_sandbox/v2/vend_tv.php';
    private $getDiscoUrl      = 'https://irecharge.com.ng/pwr_api_sandbox/v2/get_electric_disco.php';
    private $meterInfoUrl     = 'https://irecharge.com.ng/pwr_api_sandbox/v2/get_meter_info.php';
    private $vendPowerUrl     = 'https://irecharge.com.ng/pwr_api_sandbox/v2/vend_power.php';




    private $hash;

    private $vtu_amount;
    private $vtu_network;
    private $vtu_number;
    private $vtu_data;
    private $tv_network;
    private $smartcard_number;
    private $service_code;
    private $access_token;
    private $meter_number;
    private $disco;

    //set airtime variable values
    public function airtime($vtu_number, $vtu_network, $vtu_amount){
        $this->vtu_amount = $vtu_amount;
        $this->vtu_network = $vtu_network;
        $this->vtu_number = $vtu_number;
    }

    //set data top up variables
    public function data($vtu_number, $vtu_network, $vtu_data){
        $this->vtu_number = $vtu_number;
        $this->vtu_network = $vtu_network;
        $this->vtu_data = $vtu_data;
    }

    //set cable tv varibless
    public function cableTv($tv_network, $smartcard_number, $service_code, $access_token){
        $this->tv_network = $tv_network;
        $this->smartcard_number = $smartcard_number;
        $this->service_code = $service_code;
        $this->access_token = $access_token;
    }


    public function electricity($meter_number, $disco){
        $this->meter_number = $meter_number;
        $this->disco = $disco;
    }
    
    //process airtime
    public function purchaseAirtime(){
        $hashService = new IrechargeHashService(); 
        $reference_id = $hashService->genReference();
       $hash = $hashService->generateAirtimeHash($reference_id, $this->vtu_number, $this->vtu_network, $this->vtu_amount);
       $response = Http::get($this->vendAirtimeUrl,[
                    'vendor_code' => getenv('VENDOR_CODE'),
                    'vtu_number' => $this->vtu_number,
                    'reference_id' => $reference_id,
                    'vtu_amount' => $this->vtu_amount,
                    'vtu_email' => auth()->user()->email,
                    'vtu_network' => $this->vtu_network,
                    'response_format' => 'json',
                    'hash' => $hash
            ]);  
        return $response;
    }

    //get data bundle price
    public function getBundlePrice($data_network, $code){
        $response = Self::dataBundles($data_network);
        $filter = array_filter($response['bundles'], function($item) use ($code){
                    if($item['code'] == $code){
                        return $item;
                    }   
            });
        
        $values = array_values($filter);
        return $values[0]['price'];
    }

    //get data bundles
    public function dataBundles($data_network){
        try { 
            $url_response = Http::get($this->getDataBundleUrl,[
                    'data_network' => $data_network,
                    'response_format' => 'json',
            ]);
            $response = json_decode($url_response, true);
            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }    
    }

    //purchase data
    public function purchaseData(){
        $hashService = new IrechargeHashService();
        $reference_id = $hashService->genReference();
       $hash = $hashService->generateDataHash($reference_id, $this->vtu_number, $this->vtu_network, $this->vtu_data);
       $url_response = Http::get($this->buyDataBundleUrl,[
                    'vendor_code' => getenv('VENDOR_CODE'),
                    'vtu_number' => $this->vtu_number,
                    'reference_id' => $reference_id,
                    'vtu_data' => $this->vtu_data,
                    'vtu_email' => auth()->user()->email,
                    'vtu_network' => $this->vtu_network,
                    'response_format' => 'json',
                    'hash' => $hash
            ]);  
        $response = json_decode($url_response, true);
        return $response;
    }


    //Verify the status of a transaction
    //pass in service type e.g Airtime, Data, Electricity etc. and the transaction reference id
    public function checkVendStatus($service_type, $reference_id){
         //generate hash
         $hashService = new IrechargeHashService();
         $hash = $hashService->genVendStatusHash($reference_id);
    
         $url_response = Http::get($this->vendStatusUrl,[
               'vendor_code' => getenv('VENDOR_CODE'),
               'access_token' => $reference_id,
               'type' => $service_type,
               'response_format' => 'json',
               'hash' => $hash
       ]);  

       $response = json_decode($url_response, true);
       return $response;
 
    }

    //TV: GOTV, DSTV, StarTimes
    //get tv plans and Bouquet
    public function getTvPlans($tv_network){
        try {
            $url_response = Http::get($this->getTvPlansUrl,[
                'tv_network' => $tv_network,
                'response_format' => 'json',
            ]); 
            $response = json_decode($url_response, true);
            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    
    //get cable tv plan price
    public function getTvPlanPrice($tv_network, $code){
        $response = Self::getTvPlans($tv_network);
        $filter = array_filter($response['bundles'], function($item) use ($code){
                    if($item['code'] == $code){
                        return $item;
                    }   
            });
        
        $values = array_values($filter);
        return $values[0]['price'];
    }


    //get Smart Card information
    //data contains information like, tv_network GOTV, DSTV, StarTimes
    //service code and smartcard number
    public function getTvInfo($data){
        try {
            $hashService = new IrechargeHashService();
            $reference_id = $hashService->genReference();
            $hash = $hashService->getTvInfoHash($reference_id, $data);

            $url_response = Http::get($this->getTvInfoUrl,[
                    'vendor_code' => getenv('VENDOR_CODE'),
                    'smartcard_number' => $data['smartcardnumber'],
                    'reference_id' => $reference_id,
                    'service_code' => $data['service_code'],
                    'tv_network' => $data['tv_network'],
                    'response_format' => 'json',
                    'hash' => $hash
                 ]); 

            $response = json_decode($url_response, true);
          return [$response, 'reference_id' => $reference_id];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //
    public function vendTv($data){
        try {
            $hashService = new IrechargeHashService();
            $reference_id = $data['reference_id'];
            $hash = $hashService->vendTvHash($reference_id, $data);

            $url_response = Http::get($this->vendTvUrl,[
                'vendor_code' => getenv('VENDOR_CODE'),
                'smartcard_number' => $data['smartcard_number'],
                'reference_id' => $reference_id,
                'access_token' => $data['access_token'],
                'service_code' => $data['service_code'],
                'tv_network' => $data['tv_network'],
                'phone' => '08020192389',
                'email' => auth()->user()->email,
                'response_format' => 'json',
                'hash' => $hash
          ]);  

          $response = json_decode($url_response, true);
          return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    //Electricity Services
    //get all electricity supply types
    public function getDisco(){
        $url_response = Http::get($this->getDiscoUrl,[
            'response_format' => 'json',
        ]);  

      $response = json_decode($url_response, true);
      return $response;
    }

    //get meter information
    public function getMeterInfo($data){

        $hashService = new IrechargeHashService();
        $reference_id = $hashService->genReference();
        $hash = $hashService->getMeterInfoHash($reference_id, $data);
   
        $url_response = Http::get($this->meterInfoUrl,[
            'vendor_code' => getenv('VENDOR_CODE'),
            'meter' => $data['meter_number'],
            'reference_id' => $reference_id,
            'disco' => $data['disco'],
            'response_format' => 'json',
            'hash' => $hash
        ]); 
  
      $response = json_decode($url_response, true);
      return [$response, 'reference_id' => $reference_id];
    }

    //vend power
    public function vendPower($data){
        $hashService = new IrechargeHashService();
        $reference_id = $hashService->genReference();
        $hash = $hashService->vendPowerHash($reference_id, $data);
    
        $url_response = Http::get($this->vendPowerUrl,[
            'vendor_code' => getenv('VENDOR_CODE'),
            'meter' => $data['meter_number'],
            'reference_id' => $reference_id,
            'disco' => $data['disco'],
            'access_token' => $data['access_token'],
            'amount' => $data['amount'],
            'phone' => '08012920423',
            'email' => auth()->user()->email,
            'response_format' => 'json',
            'hash' => $hash
        ]);  
       
        $response = json_decode($url_response, true);
        return $response;

    }
}
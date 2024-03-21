<?php

namespace App\Services;

class IrechargeHashService
{

    //generate reference id
    public static function genReference($length = 12){
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    //airtime
    public function generateAirtimeHash($reference_id, $vtu_number, $vtu_network, $vtu_amount){
        $combined_string = getenv('VENDOR_CODE')."|". $reference_id."|".$vtu_number.
                            "|".$vtu_network."|".$vtu_amount."|".getenv('IRECHARGE_PUBLIC_KEY');

        $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
        return $hash;
    }

    //data
    public function generateDataHash($reference_id, $vtu_number, $vtu_network, $vtu_data){
        $combined_string = getenv('VENDOR_CODE')."|". $reference_id."|".$vtu_number.
                            "|".$vtu_network."|".$vtu_data."|".getenv('IRECHARGE_PUBLIC_KEY');

        $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
        return $hash;
    }


    public function genVendStatusHash($reference_id){
        $combined_string = getenv('VENDOR_CODE')."|".$reference_id."|".getenv('IRECHARGE_PUBLIC_KEY');
        $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
        return $hash; 
    }

    public function getTvInfoHash($reference_id, $data){
         //generate hash
         $combined_string = getenv('VENDOR_CODE')."|".$reference_id."|".$data['tv_network']."|".$data['smartcardnumber']."|".$data['service_code']."|".getenv('IRECHARGE_PUBLIC_KEY');
         $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
         return $hash;   
    }

    public function vendTvHash($reference_id, $data){
         //generate hash
         $combined_string = getenv('VENDOR_CODE')."|".$reference_id."|".
                            $data['smartcard_number']."|".$data['tv_network']."|".
                            $data['service_code']."|".$data['access_token']."|".getenv('IRECHARGE_PUBLIC_KEY');
         $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
         return $hash;
    }

    //generate hash for meter info
    public function getMeterInfoHash($reference_id, $data){
         //generate hash
         $combined_string = getenv('VENDOR_CODE')."|".$reference_id."|".$data['meter_number']."|".$data['disco']."|".getenv('IRECHARGE_PUBLIC_KEY');
         $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
        return $hash;
    }

    //generate hash to vend power
    public function vendPowerHash($reference_id, $data){
        $combined_string = getenv('VENDOR_CODE')."|".$reference_id."|".$data['meter_number'].
            "|".$data['disco']."|".$data['amount']."|".$data['access_token']."|".getenv('IRECHARGE_PUBLIC_KEY');
        $hash = hash_hmac("sha1", $combined_string, getenv('IRECHARGE_PRIVATE_KEY'));
        return $hash;
    }
}

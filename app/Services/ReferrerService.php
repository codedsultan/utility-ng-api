<?php

namespace App\Services;
use App\Models\User;
use Questocat\Referral\Traits\UserReferral;


class ReferrerService
{

    //update referral name
    //get referral link
    //increment and decrement user referral points
    private $referralPoints = 20;
    private $referralCode;

    public function __construct($referral_code = ''){
        $this->referralCode = $referral_code;
    }
    
    public function getReferrer(){
        return User::where('affiliate_id', $this->referralCode)->first();
    }

    //verify referral 
    public function isValidReferral(): bool 
    {
        $referer = Self::getReferrer();
        if(empty($referer)){
            return false;
        }
        return true;
    }

    //increment referrer referral points
    public function addPoints(){
        $referer = Self::getReferrer();
        $referer->referral_points += $this->referralPoints;
        $referer->save();
    }

    //return user referral link
    public function getReferralLink(){
       return  auth()->user()->getReferralLink();
    }

}
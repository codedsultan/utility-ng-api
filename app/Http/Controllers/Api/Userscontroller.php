<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;
use App\Http\Requests\UpdateProfileRequest;
use Questocat\Referral\Traits\UserReferral;
use App\Services\MyJsonResponseFormatter;
use App\Services\ReferrerService;
use App\Services\UserServices;

class Userscontroller extends Controller
{
    //view user profile
    public function viewProfile(){
        return MyJsonResponseFormatter::dataResponse(auth()->user());
    }

    //view referral link
    public function referralCode(){
        $referrerService = new ReferrerService();
        return MyJsonResponseFormatter::dataResponse($referrerService->getReferralLink()); 
    }


     //update profile
    public function updateProfile(UpdateProfileRequest $request, UserServices $userService){
        $request->validated();
        return $userService->updateProfile($request);
    }


    //convert points to cash
    public function pointsToCash(){
        $user = User::find(auth()->user()->id);
        if($user->referral_points == 0){
            return response()->json([
                'message' => 'You have no referral points, Refer your friends to get points'
            ]);
        }
        $wallet = Wallet::where('user_id', $user->id)->first(); 
        $wallet->balance += $user->referral_points;
        $wallet ->save();

        $user->referral_points = 0;
        $user->save();

        return response()->json([
            'message' => 'Your Wallet has been funded with your points',
            'data' => $wallet
        ]);
    }

}

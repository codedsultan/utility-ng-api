<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\Sanctum;
use App\Services\WalletService;
use App\Models\User;

class UserServices
{
    public $name;
    public $email;
    public $phone_number;
    private $password;
    public $referer;

    //generate user access token
    public function generateAccessToken($user, $token_name = 'user_token'){
        $accessToken = $user->createToken($token_name)->plainTextToken;
        return $accessToken;
    }

    //create new user 
    public function newUser($name, $email, $password, $phone_number, $referer = ''){
         //create new user
         $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'phone_number' => $phone_number,
            'referred_by' => $referer
        ]);
        
        //generate Access Token
        $token = Self::generateAccessToken($user);

        //create new user wallet
        $walletService = new WalletService($user->id);
        $walletService->newWallet();

        return $token;
    }

    //get user information from email && password
    public function getUserWithEmail($email){
       return User::where('email', $email)->first();
    }


    //update user profile 
    public function updateProfile(Request $request){
        $data = array();
        if(!empty($request->name)){
             $data['name'] = $request->name;
        }
       
        if(!empty($request->email)){
            if(User::where('email', $request->email)->exists()){
                return MyJsonResponseFormatter::messageResponse('This email is already taken', 400);
            }
            $data['email'] = $request->email;
        }

        if(!empty($request->phone_number)){
            if(User::where('phone_number', $request->phone_number)->exists()){
                return MyJsonResponseFormatter::messageResponse('Phone Number has been taken already', 400);
            }
            $data['phone_number'] = $request->phone_number;
        }

        if(!empty($request->referral_name)){
             if(User::where('affiliate_id', $request->referral_name)->exists()){
                return MyJsonResponseFormatter::messageResponse('This referral name is already in use', 400);
            }
            $data['affiliate_id'] = $request->referral_name;    
        }


        if(!empty($request->new_password)){
            if(!$request->current_password){
                return MyJsonResponseFormatter::messageResponse('Current Password is required', 400);
            }
            
            if( !Hash::check($request->current_password,  auth()->user()->password))
            {
                return MyJsonResponseFormatter::messageResponse('Incorrect Password', 400);
            }
            $data['password'] = Hash::make($request->new_password);    
        }

        auth()->user()->update($data);
        return MyJsonResponseFormatter::messageResponse('Profile Updated Successfully!');
    }

}
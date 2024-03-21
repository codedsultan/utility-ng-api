<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Wallet;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Services\MyJsonResponseFormatter;
use App\Services\ReferrerService;
use App\Services\UserServices;

class Authcontroller extends Controller
{

    //register with email
    public function register(RegistrationRequest $request){
        //validate request
        $request->validated(); 

        //check if the referral field is not empty
        if($request->ref != ''){
             $referralService  = new ReferrerService($request->ref);

             //if referral code is valid
             if($referralService->isValidReferral()){
                $referralService->addPoints();
                $referer = $referralService->getReferrer();
                $request->merge([
                    'ref' => $referer->email
                ]);
             }else{
                //referral code invalid
                return MyJsonResponseFormatter::messageResponse('Invalid Referral code', 422);
             }
        }
        
        //create new user, user wallet and generate access token
        $userService = new UserServices();
        $token = $userService->newUser($request->name, $request->email, $request->password, $request->phone_number, $request->ref);

        return MyJsonResponseFormatter::dataResponse($token, 'Registration Successful', 201);
    }




    //Login users with email and password
    public function login(LoginRequest $request){
            //validate request
        try{
            //validate requests
            $request->validated();
            $userService = new UserServices();
            $user =  $userService->getUserWithEmail($request->email);

            //check if user account exists
            if(is_null($user)) {  
                return MyJsonResponseFormatter::messageResponse('Your Account does not exist! Please Sign Up', 401);
            }

            //check if the passpword is correct
            if( Hash::check($request->password, $user->password) ){
                //create user login token
                $accessToken = $userService->generateAccessToken($user);
                return MyJsonResponseFormatter::dataResponse($accessToken, 'Login successful!');
            }
            
            return MyJsonResponseFormatter::messageResponse('Invalid Credientials', 400);
        }catch(\Throwable $th){
            throw $th;
        }
    }



    //social registration or login
           //facebook login
    //facebook redirect
    public function facebookRedirect(){
        return Socialite::driver('facebook')->redirect();
    }    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */


    public function facebookCallback(Request $request){   
        $user = Socialite::driver('facebook')->stateless()->user();
        $existingUser = User::where('email', $user->email)->first();
        
        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
            $accessToken = $existingUser->createToken('user_token')->plainTextToken;
            return response()->json([
                'data'=> $existingUser, 
                'token' => $accessToken
            ],200);
        } else {
           // create a new user
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'provider' => 'facebook',
                'provider_id' => $user->id,
            ]);  
 

           // login the new user
            auth()->login($newUser, true);
            $accessToken = $newUser->createToken('user_token')->plainTextToken;
            return response()->json(['data' => $newUser, 'token' =>$accessToken], 200);
        }
      
        return response()->json(['message'=> 'Unable to get your Account Information'], 401);
    }


    //google login
    //google redirect
    /**
  * Redirect the user to the Google authentication page.
  *
  * @return \Illuminate\Http\Response
  */
    public function googleRedirect(){
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function googleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        $existingUser = User::where('email', $user->email)->first();      
        
        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
            $accessToken = $existingUser->createToken('user_token')->plainTextToken;
            return response()->json([
                'data'=> $existingUser, 
                'token' => $accessToken
            ],200);
        } else {
           // create a new user
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'provider' => 'google',
                'provider_id' => $user->id,
            ]);  
 

           // login the new user
            auth()->login($newUser, true);
            $accessToken = $newUser->createToken('user_token')->plainTextToken;
            return response()->json(['data' => $newUser, 'token' =>$accessToken], 200);
        }
      
        return response()->json(['message'=> 'Unable to get your Account Information'], 401);
    }

     public function logout(){
        //revoke the user token on log out
        auth()->user()->tokens()->delete();
        return MyJsonResponseFormatter::messageResponse('Logged Out Successfully');
    }
}

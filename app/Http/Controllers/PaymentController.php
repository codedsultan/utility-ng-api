<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Paystack;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Payment;

class PaymentController extends Controller
{
    // public function redirectToGateway(Request $request){

    //      try{
    //          $newval = 20000;
    //         $request->merge([
    //             'amount' => $newval,
    //         ]);
    //         return Paystack::getAuthorizationUrl()->redirectNow();
    //    }catch(\Exception $e) {
    //         return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
    //     }        
    // }
    //paystack callback
    public function handleGatewayCallback(Request $request){
        $paymentDetails = Paystack::getPaymentData($request->reference);
      
        $reference_id = $paymentDetails['data']['reference'];

        //amount is in kobo so divide by 100
        $wallet_amount = $paymentDetails['data']['amount'] / 100; 

        //create transaction record
        $transaction = Payment::create([
            'user_id' => auth()->user()->id,
            'trxref' => $reference_id,
            'amount' => $wallet_amount
        ]);

        //update the user wallet balance 
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        $wallet->balance += $wallet_amount;
        $wallet->save();
        return response()->json([
            'message' =>  'You have successfully funded your wallet',
            'data' => $transaction
        ]);
    }



    //show user payment history
    public function paymentHistory(){
        $payment_history = Payment::orderBy('created_at', 'desc')
                                ->where('user_id', auth()->user()->id)
                                ->paginate(20);
        
        return response()->json([
            'data' => $payment_history
        ]);
    }


}

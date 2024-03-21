<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;


class WalletController extends Controller
{
    //show in app wallet balance
    public function balance(){
        $wallet_balance = Wallet::where('user_id', auth()->user()->id)->first();
        return response()->json([
            'data' => $wallet_balance
        ]);
    }


    //show user transaction history
    public function transactionHistory(){
        $transaction_history = Transaction::latest()
                                ->where('user_id', auth()->user()->id)
                                ->paginate(20);
        
        return response()->json([
            'data' => $transaction_history
        ]);
    }
    //admin check wallet Balance

}



<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedCards;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SavedCardsController extends Controller
{
    //view all current user cards
    public function allCards(){
        try{
             $cards = SavedCards::where('user_id', auth()->user()->id)->get();
             if(sizeof($cards) == 0){
                return response()->json([
                    'message' => 'There are currently no saved cards!' 
                ]);
             }
                if($cards[0]->user_id !== auth()->user()->id){
                        return response()->json([
                            'error' => 'Unauthorized Access'
                            ], 401);
                        }
                    return response()->json([
                        'data' => $cards
                    ]);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);   
        }
       
    }


    //find card by id
    public function showCard($id){
        try{ 
            $card = SavedCards::findOrFail($id);
            if($card->user_id !== auth()->user()->id){
                return response()->json([
                    'error' => 'Unauthorized Access'
                ], 401);
            }
            return response()->json([
                'data' => $card
            ]);

        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);

        }
       
    }

    //save a new card
    public function newCard(Request $request){
        try{
             $request->validate([
                        'card_number' => 'required',
                        'expiry' => 'required'
                    ]);

                    $addCard = SavedCards::create([
                        'user_id' => auth()->user()->id,
                        'card_number' => $request->card_number,
                        'expiry' => $request->expiry
                    ]);

            return response()->json([
                'message' => 'New card saved successfully',
                        'data' => $addCard
                    ], 201);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
       
    }


    //update card information
    public function updateCard(Request $request, $id){
        try{
             $request->validate([
                        'card_number' => 'required',
                        'expiry' => 'required'
                    ]);
                    $card = SavedCards::findOrFail($id);
                    $card->card_number = $request->card_number;
                    $card->expiry = $request->expiry;
                    $card->save();

                    return response()->json([
                        'message' => 'Card Updated Successfully',
                        'data' => $card
                    ],200); 
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }


    //delete a card
    public function deleteCard($id){
        $card = SavedCards::findOrFail($id)->delete();
        return response()->json(['message' => 'Card deleted!'], 200);
    }
}

<?php

namespace App\Services;

use App\Models\SavedCards;


class SaveCardService
{
    //get user saved meters
    public function getSavedCards(){
       return SavedMeter::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->get();
    }


    //find meter by if
    public function showCard(SavedCard $card){
        return $meter;
    }

    public function newCard($card_number, $disco){
        
        $card = SavedCard::firstOrCreate([
            'user_id' => auth()->user()->id,
            'meter_number' => $meter_number,
            'disco' => $disco,
        ]);
        return $meter;
    }

    //update meter
    public function updateMeter($meter_id, $meter_number, $disco){
        $data = [
            'meter_number' => $meter_number,
            'disco' => $disco
        ];
        $meter = SavedMeter::findOrFail($meter_id);
        $meter->update($data);
        return $meter;
    }

    //delete meter
    public function deleteMeter($id){
        $meter = SavedMeter::findOrFail($id)->delete();
        return 'Meter Deleted Successfully!';
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedMeter;
use App\Services\SaveMeterService;
use App\Services\MyJsonResponseFormatter;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\NewMeterRequest;
use App\Models\User;

class SavedMeterController extends Controller
{
     //view all current user meters
    public function allMeter(){
        try{

            $saveMeterService = new SaveMeterService();
            return MyJsonResponseFormatter::dataResponse($saveMeterService->getSavedMeters());
            
        }catch(\Throwable $th){
            throw $th;
        }
    }

      //save a new card
    public function newMeter(NewMeterRequest $request, SavedMeterService $saveMeterService){
        try{

            $request->validated();
            $newMeter = $saveMeterService->newMeter($request->meter_number, $disco);
            return MyJsonResponseFormatter::dataResponse($newMeter, 'Meter Saved Successfully!', 201);

        }catch(\Throwable $th){
            throw $th;
        }
       
    }

    //find card by id
    public function showMeter($id){
        $meter = SavedMeter::findOrFail($id);
        return MyJsonResponseFormatter::dataResponse($meter);
    }
    
    //update card information
    public function updateMeter(NewMeterRequest $request, $id){
        try{
             $request->validated();
            
             $saveMeterService = new SaveMeterService();
             $meter = $saveMeterService->updateMeter($id, $request->meter_number, $request->disco);
             return MyJsonResponseFormatter::dataResponse($meter, 'Meter Updated Successfully!');
        }catch(\Throwable $th){
            throw $th;
        }
        
    }


    //delete a card
    public function deleteMeter($id){
        try{
            $meter = SavedMeter::findOrFail($id)->delete();
            return MyJsonResponseFormatter::messageResponse('Meter Deleted Successfully!');
        }catch(\Throwable $th){
            throw $th;
        }
        
    }
}

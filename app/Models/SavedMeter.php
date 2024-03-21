<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedMeter extends Model
{
    use HasFactory;
    protected $table = 'saved_meters';
    protected $fillable = [
        'user_id',
        'meter_number',
        'disco'
        
    ];
}

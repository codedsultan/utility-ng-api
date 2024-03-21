<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedCards extends Model
{
    use HasFactory;
    protected $table = 'saved_cards';
    protected $fillable = [
        'user_id',
        'card_number',
        'expiry'  
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'user_id',
        'phone_number',
        'amount',
        'type',
        'meter_number',
        'meter_type',
        'smartcard_number',
        'tv_network',
        'vtu_network',
        'vtu_data',
        'reference'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

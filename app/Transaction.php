<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'card_number',
        'type',
        'status',
        'amount',
        'observation'
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'type',
    ];


    public function account()
    {
        return $this->hasMany('App\Transaction');
    }
}

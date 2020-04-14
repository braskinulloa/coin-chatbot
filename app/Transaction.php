<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'type', 'amount', 'currency', 'balance'
    ];

    public $timestamps = true;
}

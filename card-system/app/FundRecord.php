<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FundRecord extends Model
{
    protected $guarded = [];
    const TYPE_IN  = 1;
    const TYPE_OUT = 2;

    function order()
    {
        return $this->belongsTo(Order::class);
    }
}
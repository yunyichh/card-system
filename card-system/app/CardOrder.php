<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CardOrder extends Model
{
    protected $table = 'card_order';
    public $timestamps = false;

    function order()
    {
        return $this->belongsTo(Order::class);
    }

    function card()
    {
        return $this->belongsTo(Card::class)->withTrashed();
    }
}
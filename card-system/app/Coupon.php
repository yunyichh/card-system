<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];
    const STATUS_NORMAL         = 0;
    const STATUS_USED           = 2;
    const TYPE_ONETIME          = 0;
    const TYPE_REPEAT           = 1;
    const DISCOUNT_TYPE_AMOUNT  = 0;
    const DISCOUNT_TYPE_PERCENT = 1;

    function product()
    {
        return $this->belongsTo(Product::class);
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }
}
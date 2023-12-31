<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Card extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    const STATUS_NORMAL = 0;
    const STATUS_SOLD   = 1;
    const STATUS_USED   = 2;
    const TYPE_ONETIME  = 0;
    const TYPE_REPEAT   = 1;

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function product()
    {
        return $this->belongsTo(Product::class);
    }

    function getCountAttribute()
    {
        return $this->count_all - $this->count_sold;
    }

    public static function add_cards($spe0b9a0, $sp5eed44, $spa8b0dd, $sp7acd11, $spbd612e, $sp51bf52)
    {
        DB::statement('call add_cards(?,?,?,?,?,?)', [
            $spe0b9a0,
            $sp5eed44,
            $spa8b0dd,
            $sp7acd11,
            $spbd612e,
            (int)$sp51bf52,
        ]);
    }

    public static function _trash($sp4210ad)
    {
        DB::transaction(function () use ($sp4210ad) {
            $sp7c86af = clone $sp4210ad;
            $sp7c86af->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count_left`')->groupBy('product_id')
                ->orderByRaw('`product_id`')->chunk(100, function ($sp880564) {
                foreach ($sp880564 as $spf67141) {
                    $spfd49bd = \App\Product::where('id', $spf67141->product_id)->lockForUpdate()->first();
                    if ($spfd49bd) {
                        $spfd49bd->count_all -= $spf67141->count_left;
                        $spfd49bd->saveOrFail();
                    }
                }
            });
            $sp4210ad->delete();

            return true;
        });
    }

    public static function _restore($sp4210ad)
    {
        DB::transaction(function () use ($sp4210ad) {
            $sp7c86af = clone $sp4210ad;
            $sp7c86af->selectRaw('`product_id`,SUM(`count_all`-`count_sold`) as `count_left`')->groupBy('product_id')
                ->orderByRaw('`product_id`')->chunk(100, function ($sp880564) {
                foreach ($sp880564 as $spf67141) {
                    $spfd49bd = \App\Product::where('id', $spf67141->product_id)->lockForUpdate()->first();
                    if ($spfd49bd) {
                        $spfd49bd->count_all += $spf67141->count_left;
                        $spfd49bd->saveOrFail();
                    }
                }
            });
            $sp4210ad->restore();

            return true;
        });
    }
}
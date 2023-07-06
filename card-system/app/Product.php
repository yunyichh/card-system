<?php

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log as LogWriter;

class Product extends Model
{
    protected $guarded = [];
    protected $hidden = [];
    const ID_API          = -1001;
    const DELIVERY_AUTO   = 0;
    const DELIVERY_MANUAL = 1;
    const DELIVERY_API    = 2;

    function getUrlAttribute()
    {
        return config('app.url').'/p/'.Helper::id_encode($this->id, Helper::ID_TYPE_PRODUCT);
    }

    function getCountAttribute()
    {
        return $this->count_all - $this->count_sold;
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function cards()
    {
        return $this->hasMany(Card::class);
    }

    function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function refreshCount($spbfa519)
    {
        \App\Card::where('user_id', $spbfa519->id)
            ->selectRaw('`product_id`,SUM(`count_sold`) as `count_sold`,SUM(`count_all`) as `count_all`')
            ->groupBy('product_id')->orderByRaw('`product_id`')->chunk(1000, function ($sp880564) {
            foreach ($sp880564 as $spf67141) {
                $spfd49bd = \App\Product::where('id', $spf67141->product_id)->first();
                if ($spfd49bd) {
                    if ($spfd49bd->delivery === \App\Product::DELIVERY_MANUAL) {
                        $spfd49bd->update(['count_sold' => $spf67141->count_sold]);
                    } else {
                        $spfd49bd->update(['count_sold' => $spf67141->count_sold, 'count_all' => $spf67141->count_all]);
                    }
                } else {
                }
            }
        });
    }

    function createApiCards($sp63ddab)
    {
        $sp148704 = [];
        $sp087aeb = [];
        $spe2b023 = [];
        for ($sp558f52 = 0; $sp558f52 < $sp63ddab->count; $sp558f52++) {
            $sp148704[] = strtoupper(str_random(16));
            $sp7f8c48   = date('Y-m-d H:i:s');
            switch ($this->id) {
                case 6:
                    $spad3961 = 1;
                    break;
                case 11:
                    $spad3961 = 2;
                    break;
                case 37:
                    $spad3961 = 3;
                    break;
                default:
                    die('App.Products fatal error#1');
            }
            $spe2b023[] = [
                'user_id'    => $this->user_id,
                'product_id' => $this->id,
                'card'       => $sp148704[$sp558f52],
                'type'       => \App\Card::TYPE_ONETIME,
                'status'     => \App\Card::STATUS_NORMAL,
                'count_sold' => 0,
                'count_all'  => 1,
            ];
            $sp087aeb[] = "(NULL, '{$sp148704[$sp558f52]}', '1', '{$spad3961}', NULL, NULL, NULL, NULL, NULL, '0', '{$sp7f8c48}', '0000-00-00 00:00:00')";
        }
        $spd90da1 = mysqli_connect('localhost', 'udiddz', 'tRihPm3sh6yKedtX', 'udiddz', '3306');
        $spa65f4b = 'INSERT INTO `udiddz`.`ac_kms` (`id`, `km`, `value`, `task`, `udid`, `diz`, `task_id`, `install_url`, `plist_url`, `jh`, `addtime`, `tjtime`) VALUES '
                    .join(',', $sp087aeb);
        $sp6584c8 = mysqli_query($spd90da1, $spa65f4b);
        if (!$sp6584c8) {
            LogWriter::error('App.Products, connect udid database failed', [
                'sql'   => $spa65f4b,
                'error' => mysqli_error($spd90da1),
            ]);

            return [];
        }
        $this->count_all += $sp63ddab->count;

        return $this->cards()->createMany($spe2b023);
    }

    function setForShop($spbfa519 = null)
    {
        $spfd49bd = $this;
        $sp951cdc = $spfd49bd->count;
        $spf6dc54 = $spfd49bd->inventory;
        if ($spf6dc54 == User::INVENTORY_AUTO) {
            $spf6dc54 = System::_getInt('shop_inventory');
        }
        if ($spf6dc54 == User::INVENTORY_RANGE) {
            if ($sp951cdc <= 0) {
                $sp66527c = '不足';
            } elseif ($sp951cdc <= 10) {
                $sp66527c = '少量';
            } elseif ($sp951cdc <= 20) {
                $sp66527c = '一般';
            } else {
                $sp66527c = '大量';
            }
            $spfd49bd->setAttribute('count2', $sp66527c);
        } else {
            $spfd49bd->setAttribute('count2', $sp951cdc);
        }
        $spfd49bd->setAttribute('count', $sp951cdc);
        $spfd49bd->setVisible([
            'id',
            'name',
            'description',
            'fields',
            'delivery',
            'count',
            'count2',
            'buy_min',
            'buy_max',
            'support_coupon',
            'password_open',
            'price',
            'price_whole',
        ]);
    }
}
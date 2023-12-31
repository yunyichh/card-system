<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $appends = ['m_balance', 'role'];
    protected $casts = ['theme_config' => 'array'];
    const ID_CUSTOMER       = -1;
    const INVENTORY_RANGE   = 0;
    const INVENTORY_REAL    = 1;
    const INVENTORY_AUTO    = 2;
    const FEE_TYPE_MERCHANT = 0;
    const FEE_TYPE_CUSTOMER = 1;
    const FEE_TYPE_AUTO     = 2;
    const STATUS_OK         = 0;
    const STATUS_FROZEN     = 1;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($sp97507d)
    {
        throw new \Exception('unimplemented in free version');
    }

    function getMBalanceAttribute()
    {
        return $this->m_all - $this->m_paid - $this->m_frozen;
    }

    function getRoleAttribute()
    {
        return 'admin';
    }

    function getMBalanceWithoutTodayAttribute()
    {
        $spe4e277 = (int)\App\Order::where('user_id', $this->user_id)->where('status', \App\Order::STATUS_SUCCESS)
            ->whereDate('paid_at', Carbon::today())->sum('income');

        return $this->m_all - $this->m_paid - $this->m_frozen - $spe4e277;
    }

    function getShopThemeAttribute()
    {
        if ($this->theme_config && isset($this->theme_config['theme'])) {
            $spa59707 = \App\ShopTheme::whereName($this->theme_config['theme'])->first();
            if ($spa59707) {
                return $spa59707;
            }
        }

        return \App\ShopTheme::defaultTheme();
    }

    function getLastLoginAtAttribute()
    {
        $spf008a0 = $this->logs()->where('action', \App\Log::ACTION_LOGIN)->orderBy('id', 'DESC')->first();

        return $spf008a0 ? $spf008a0->address ?? $spf008a0->ip : null;
    }

    function categories()
    {
        return $this->hasMany(Category::class);
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }

    function cards()
    {
        return $this->hasMany(Card::class);
    }

    function orders()
    {
        return $this->hasMany(Order::class);
    }

    function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    function logs()
    {
        return $this->hasMany(Log::class);
    }

    function shop_theme()
    {
        return $this->belongsTo(ShopTheme::class);
    }
}
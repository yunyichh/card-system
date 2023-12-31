<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pay extends Model
{
    protected $guarded = [];

    public static function gets()
    {
        return Cache::remember('model.pays', 10, function () {
            return self::query()->get();
        });
    }

    public static function flushCache()
    {
        Cache::forget('model.pays');
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function () {
            self::flushCache();
        });
        static::created(function () {
            self::flushCache();
        });
        static::deleted(function () {
            self::flushCache();
        });
    }
}
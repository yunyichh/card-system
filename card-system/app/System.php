<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class System extends Model
{
    protected $guarded = [];
    private static $systems = [];

    public static function _init()
    {
        static::$systems                 = Cache::remember('settings.all', 10, function () {
            $sped3522 = self::query()->get()->toArray();
            foreach ($sped3522 as $sp0e8834) {
                static::$systems[$sp0e8834['name']] = $sp0e8834['value'];
            }

            return static::$systems;
        });
        static::$systems['_initialized'] = true;
    }

    public static function _get($spb54a76, $sp70cfab = null)
    {
        if (!isset(static::$systems['_initialized'])) {
            static::_init();
        }
        if (isset(static::$systems[$spb54a76])) {
            return static::$systems[$spb54a76];
        }

        return $sp70cfab;
    }

    public static function _getInt($spb54a76, $sp70cfab = null)
    {
        return (int)static::_get($spb54a76, $sp70cfab);
    }

    public static function _set($spb54a76, $spa60c0f)
    {
        static::$systems[$spb54a76] = $spa60c0f;
        $spb83345                   = System::query()->where('name', $spb54a76)->first();
        if ($spb83345) {
            $spb83345->value = $spa60c0f;
            $spb83345->save();
        } else {
            try {
                System::query()->create(['name' => $spb54a76, 'value' => $spa60c0f]);
            } catch (\Exception $spc22b6c) {
            }
        }
        self::flushCache();
    }

    public static function flushCache()
    {
        Cache::forget('settings.all');
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
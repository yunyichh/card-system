<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopTheme extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $casts = ['options' => 'array', 'config' => 'array'];
    private static $default_theme;

    public static function defaultTheme()
    {
        if (!static::$default_theme) {
            static::$default_theme = ShopTheme::query()
                ->where('name', \App\System::_get('shop_theme_default', 'Material'))->first();
            if (!static::$default_theme) {
                static::$default_theme = ShopTheme::query()->firstOrFail();
            }
        }

        return static::$default_theme;
    }

    public static function freshList()
    {
        $spab6f31 = realpath(app_path('..'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'
                                      .DIRECTORY_SEPARATOR.'shop_theme'));
        \App\ShopTheme::query()->get()->each(function ($sp2307ff) use ($spab6f31) {
            if (!file_exists($spab6f31.DIRECTORY_SEPARATOR.$sp2307ff->name.DIRECTORY_SEPARATOR.'config.php')) {
                $sp2307ff->delete();
            }
        });
        foreach (scandir($spab6f31) as $sp13204e) {
            if ($sp13204e === '.' || $sp13204e === '..') {
                continue;
            }
            try {
                @($sp2307ff = (include $spab6f31.DIRECTORY_SEPARATOR.$sp13204e.DIRECTORY_SEPARATOR.'config.php'));
            } catch (\Exception $spc22b6c) {
                continue;
            }
            $sp2307ff['config'] = array_map(function ($spf1bfa9) {
                return $spf1bfa9['value'];
            }, @$sp2307ff['options'] ?? []);
            $spdbdd55           = \App\ShopTheme::query()->where('name', $sp13204e)->first();
            if ($spdbdd55) {
                $spdbdd55->description = $sp2307ff['description'];
                $spdbdd55->options     = @$sp2307ff['options'] ?? [];
                $spdbdd55->config      = ($spdbdd55->config ?? []) + $sp2307ff['config'];
                $spdbdd55->saveOrFail();
            } else {
                if ($sp2307ff && isset($sp2307ff['description'])) {
                    \App\ShopTheme::query()->create(['name'        => $sp13204e,
                                                     'description' => $sp2307ff['description'],
                                                     'options'     => @$sp2307ff['options'] ?? [],
                                                     'config'      => $sp2307ff['config'],
                    ]);
                }
            }
        }
    }
}
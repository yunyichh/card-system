<?php

namespace App;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;

class PayWay extends Model
{
    protected $guarded = [];
    protected $casts = ['channels' => 'array'];
    const ENABLED_DISABLED = 0;
    const ENABLED_PC       = 1;
    const ENABLED_MOBILE   = 2;
    const ENABLED_ALL      = 3;
    const TYPE_SHOP        = 1;
    const TYPE_API         = 2;

    public function getPayByWeight()
    {
        $sp951cdc = $sp2a5b17 = 0;
        $sp03525f = [];
        $spb0cc9a = [];
        foreach ($this->channels as $spf1bfa9) {
            $spb0cc9a[] = intval($spf1bfa9[0]);
        }
        $spc35bd5 = \App\Pay::gets()->filter(function ($sp9a29e9) use ($spb0cc9a) {
            return in_array($sp9a29e9->id, $spb0cc9a);
        });
        $spffafb1 = [];
        foreach ($spc35bd5 as $spb3a6bf) {
            $spffafb1[$spb3a6bf->id] = $spb3a6bf;
        }
        foreach ($this->channels as $spf1bfa9) {
            $sp9208ec = intval($spf1bfa9[0]);
            $sp339be4 = intval($spf1bfa9[1]);
            if ($sp339be4 && isset($spffafb1[$sp9208ec]) && $spffafb1[$sp9208ec]->enabled > 0) {
                $sp951cdc   += $sp339be4;
                $spf52f26   = $sp2a5b17 + $sp339be4;
                $sp03525f[] = ['min' => $sp2a5b17, 'max' => $spf52f26, 'pay_id' => $sp9208ec];
                $sp2a5b17   = $spf52f26;
            }
        }
        if ($sp951cdc <= 0) {
            return null;
        }
        $spb3ada3 = mt_rand(0, $sp951cdc - 1);
        foreach ($sp03525f as $spf366de) {
            if ($spf366de['min'] <= $spb3ada3 && $spb3ada3 < $spf366de['max']) {
                return $spffafb1[$spf366de['pay_id']];
            }
        }

        return null;
    }

    public static function gets($spbfa519, $sp5b92ec = null)
    {
        $sp4210ad = self::query();
        if ($sp5b92ec !== null) {
            $sp4210ad->where($sp5b92ec);
        }
        $sp1db360 = $sp4210ad->orderBy('sort')->get(['name', 'img', 'channels']);
        $spbadaff = [];
        foreach ($sp1db360 as $spcc50ed) {
            $sp9a29e9 = $spcc50ed->getPayByWeight();
            if ($sp9a29e9) {
                $sp9a29e9->setAttribute('name', $spcc50ed->name);
                $sp9a29e9->setAttribute('img', $spcc50ed->img);
                $sp9a29e9->setVisible(['id', 'name', 'img', 'fee']);
                $spbadaff[] = $sp9a29e9;
            }
        }

        return $spbadaff;
    }
}
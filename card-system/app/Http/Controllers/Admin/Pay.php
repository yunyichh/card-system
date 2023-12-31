<?php

namespace App\Http\Controllers\Admin;

use App\Library\Helper;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;

class Pay extends Controller
{
    function get(Request $sp62e4cd)
    {
        $sp4210ad = \App\Pay::query();
        $sp3022f1 = $sp62e4cd->input('enabled');
        if (strlen($sp3022f1)) {
            $sp4210ad->whereIn('enabled', explode(',', $sp3022f1));
        }
        $spe0aaed = $sp62e4cd->input('search', false);
        $sp75c4d8 = $sp62e4cd->input('val', false);
        if ($spe0aaed && $sp75c4d8) {
            if ($spe0aaed == 'simple') {
                return Response::success($sp4210ad->get(['id', 'name', 'enabled', 'comment']));
            } elseif ($spe0aaed == 'id') {
                $sp4210ad->where('id', $sp75c4d8);
            } else {
                $sp4210ad->where($spe0aaed, 'like', '%'.$sp75c4d8.'%');
            }
        }
        $sp6492f8 = $sp4210ad->get();

        return Response::success([
            'list' => $sp6492f8,
            'urls' => ['url' => config('app.url'), 'url_api' => config('app.url_api')],
        ]);
    }

    function stat(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, ['day' => 'required|integer|between:1,30']);
        $sp0d78d7 = (int)$sp62e4cd->input('day');
        if ($sp0d78d7 === 30) {
            $sp20bce8 = Carbon::now()->addMonths(-1)->toDateString().' 00:00:00';
        } else {
            $sp20bce8 = Carbon::now()->addDays(-$sp0d78d7)->toDateString().' 00:00:00';
        }
        $sp6492f8 = $this->authQuery($sp62e4cd, \App\Order::class)->where(function ($sp4210ad) {
            $sp4210ad->where('status', \App\Order::STATUS_PAID)->orWhere('status', \App\Order::STATUS_SUCCESS);
        })->where('paid_at', '>=', $sp20bce8)->with([
            'pay' => function ($sp4210ad) {
                $sp4210ad->select(['id', 'name']);
            },
        ])->groupBy('pay_id')->selectRaw('`pay_id`,COUNT(*) as "count",SUM(`paid`) as "sum"')->get()->toArray();
        $spa59707 = [];
        foreach ($sp6492f8 as $spf1bfa9) {
            if (isset($spf1bfa9['pay']) && isset($spf1bfa9['pay']['name'])) {
                $spe192c7 = $spf1bfa9['pay']['name'];
            } else {
                $spe192c7 = '未知方式#'.$spf1bfa9['pay_id'];
            }
            $spa59707[$spe192c7] = [(int)$spf1bfa9['count'], (int)$spf1bfa9['sum']];
        }

        return Response::success($spa59707);
    }

    function edit(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, [
            'id'         => 'sometimes|integer',
            'name'       => 'required|string',
            'driver'     => 'required|string',
            'way'        => 'required|string',
            'config'     => 'required|string',
            'fee_system' => 'required|numeric',
        ]);
        $sp9a29e9 = \App\Pay::find((int)$sp62e4cd->post('id'));
        if (!$sp9a29e9) {
            $sp9a29e9 = new \App\Pay();
        }
        $sp9a29e9->name       = $sp62e4cd->post('name');
        $sp9a29e9->comment    = $sp62e4cd->post('comment');
        $sp9a29e9->driver     = $sp62e4cd->post('driver');
        $sp9a29e9->way        = $sp62e4cd->post('way');
        $sp9a29e9->config     = $sp62e4cd->post('config');
        $sp9a29e9->enabled    = (int)$sp62e4cd->post('enabled');
        $sp9a29e9->fee_system = $sp62e4cd->post('fee_system');
        $sp9a29e9->saveOrFail();

        return Response::success();
    }

    function comment(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, ['id' => 'required|integer']);
        $spdc31ea          = (int)$sp62e4cd->post('id');
        $sp9a29e9          = \App\Pay::findOrFail($spdc31ea);
        $sp9a29e9->comment = $sp62e4cd->post('comment');
        $sp9a29e9->save();

        return Response::success();
    }

    function fee_system(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, ['id' => 'required|integer']);
        $spdc31ea             = (int)$sp62e4cd->post('id');
        $sp9a29e9             = \App\Pay::findOrFail($spdc31ea);
        $sp9a29e9->fee_system = $sp62e4cd->post('fee_system');
        $sp9a29e9->saveOrFail();

        return Response::success();
    }

    function enable(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, ['ids' => 'required|string', 'enabled' => 'required|integer|between:0,3']);
        $spb0cc9a = $sp62e4cd->post('ids');
        $sp3022f1 = (int)$sp62e4cd->post('enabled');
        \App\Pay::whereIn('id', explode(',', $spb0cc9a))->update(['enabled' => $sp3022f1]);
        \App\Pay::flushCache();

        return Response::success();
    }

    function delete(Request $sp62e4cd)
    {
        $this->validate($sp62e4cd, ['ids' => 'required|string']);
        $spb0cc9a = $sp62e4cd->post('ids');
        \App\Pay::whereIn('id', explode(',', $spb0cc9a))->delete();
        \App\Pay::flushCache();

        return Response::success();
    }
}
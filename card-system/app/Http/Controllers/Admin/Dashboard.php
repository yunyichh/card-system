<?php

namespace App\Http\Controllers\Admin;

use App\Library\CurlRequest;
use App\Library\Response;
use App\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Dashboard extends Controller
{
    function index(Request $sp62e4cd)
    {
        $spbd9407 = [
            'today'     => ['count' => 0, 'paid' => 0, 'profit' => 0],
            'yesterday' => ['count' => 0, 'paid' => 0, 'profit' => 0],
        ];
        $sp9b6ca7 = Order::whereUserId(\Auth::Id())->whereDate('paid_at', \Carbon\Carbon::now()->toDateString())
            ->where(function ($sp4210ad) {
                $sp4210ad->where('status', Order::STATUS_PAID)->orWhere('status', Order::STATUS_SUCCESS);
            })->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')
            ->get()->toArray();
        $sp62d8c0 = Order::whereUserId(\Auth::Id())->whereDate('paid_at', \Carbon\Carbon::yesterday()->toDateString())
            ->where(function ($sp4210ad) {
                $sp4210ad->where('status', Order::STATUS_PAID)->orWhere('status', Order::STATUS_SUCCESS);
            })->selectRaw('COUNT(*) as `count`,SUM(`paid`) as `paid`,SUM(`paid`-`sms_price`-`cost`-`fee`) as `profit`')
            ->get()->toArray();
        if (isset($sp9b6ca7[0]) && isset($sp9b6ca7[0]['count'])) {
            $spbd9407['today'] = [
                'count'  => (int)$sp9b6ca7[0]['count'],
                'paid'   => (int)$sp9b6ca7[0]['paid'],
                'profit' => (int)$sp9b6ca7[0]['profit'],
            ];
        }
        if (isset($sp62d8c0[0]) && isset($sp62d8c0[0]['count'])) {
            $spbd9407['yesterday'] = [
                'count'  => (int)$sp62d8c0[0]['count'],
                'paid'   => (int)$sp62d8c0[0]['paid'],
                'profit' => (int)$sp62d8c0[0]['profit'],
            ];
        }
        $spbd9407['need_ship_count'] = Order::whereUserId(\Auth::Id())->where('status', Order::STATUS_PAID)->count();
        $spbd9407['login']           = \App\Log::where('action', \App\Log::ACTION_LOGIN)->latest()->first();

        return Response::success($spbd9407);
    }

    function clearCache()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('route:cache');
            \Artisan::call('config:cache');
        } catch (\Throwable $spc22b6c) {
            return Response::fail($spc22b6c->getMessage());
        }

        return Response::success();
    }

    function version()
    {
        $sp8ebf4f = [];
        $sp409cb9 = CurlRequest::curl('https://raw.githubusercontent.com/Tai7sy/card-system/master/.version', 0, null, [], 5, $spc94a6d, $sp8eaaf0, $sp827c9d);
        $spd94816 = @json_decode($sp409cb9, true);
        if (!@$spd94816['data']['version']) {
            Log::error('Dashboard.version checkUpdate failed', [
                'response' => $sp409cb9,
                'httpCode' => $sp8eaaf0,
                'error'    => $sp827c9d,
            ]);
            $sp8ebf4f['message'] = '检查更新失败: '.$sp827c9d;
        } else {
            $sp8ebf4f = [
                'version'     => $spd94816['data']['version'],
                'description' => @$spd94816['data']['description'] ?? '无',
            ];
        }

        return Response::success(['version' => config('app.version'), 'update' => $sp8ebf4f]);
    }

    function logsToken()
    {
        $sp97507d = md5(random_bytes(128));
        Cache::put($sp97507d, Auth::getUser(), 15);

        return response(['token' => $sp97507d]);
    }

    function logsView(Request $sp62e4cd, $sp97507d)
    {
        if ($spbfa519 = Cache::get($sp97507d)) {
            Cache::put($sp97507d, $spbfa519, 15);

            return (new \Rap2hpoutre\LaravelLogViewer\LogViewerController())->index();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('logs-token');
        }
    }
}
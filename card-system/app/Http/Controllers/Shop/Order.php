<?php

namespace App\Http\Controllers\Shop;

use App\System;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;
use App\Library\Geetest;
use Illuminate\Support\Facades\Cookie;

class Order extends Controller
{
    function get(Request $sp62e4cd)
    {
        if (\App\System::_getInt('vcode_shop_search') === 1) {
            $this->validateCaptcha($sp62e4cd);
        }
        $sp4210ad = \App\Order::where('created_at', '>=', (new Carbon())->addDay(-\App\System::_getInt('order_query_day', 30)));
        $spa8b0dd = $sp62e4cd->post('type', '');
        if ($spa8b0dd === 'cookie') {
            $sp02ae43 = Cookie::get('customer');
            if (strlen($sp02ae43) !== 32) {
                return Response::success();
            }
            $sp4210ad->where('customer', $sp02ae43);
        } elseif ($spa8b0dd === 'order_no') {
            $sp592eb0 = $sp62e4cd->post('order_no', '');
            if (strlen($sp592eb0) !== 19) {
                return Response::success();
            }
            $sp4210ad->where('order_no', $sp592eb0);
        } elseif ($spa8b0dd === 'contact') {
            $sp0e200d = $sp62e4cd->post('contact', '');
            if (strlen($sp0e200d) < 6) {
                return Response::success();
            }
            $sp4210ad->where('contact', $sp0e200d);
            if (System::_getInt('order_query_password_open')) {
                $sp0f0e2a = $sp62e4cd->post('query_password', '');
                if (strlen($sp0f0e2a) < 6) {
                    return Response::success();
                }
                $sp4210ad->where('query_password', $sp0f0e2a);
            }
        } else {
            return Response::fail(trans('shop.search_type.required'));
        }
        $sp33e83b = ['id', 'created_at', 'order_no', 'contact', 'status', 'send_status', 'count', 'paid'];
        if (1) {
            $sp33e83b[] = 'product_name';
            $sp33e83b[] = 'contact';
            $sp33e83b[] = 'contact_ext';
        }
        $sp6492f8 = $sp4210ad->orderBy('id', 'DESC')->get($sp33e83b);
        $sp6755e6 = '';

        return Response::success(['list' => $sp6492f8, 'msg' => count($sp6492f8) ? $sp6755e6 : '']);
    }
}
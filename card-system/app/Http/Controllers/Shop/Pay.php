<?php

namespace App\Http\Controllers\Shop;

use App\Card;
use App\Category;
use App\Library\FundHelper;
use App\Library\Helper;
use App\Library\LogHelper;
use App\LibraryLogHelper;
use App\Product;
use App\Library\Response;
use Gateway\Pay\Pay as GatewayPay;
use App\Library\Geetest;
use App\Mail\ProductCountWarn;
use App\System;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Pay extends Controller
{
    public function __construct()
    {
        define('SYS_NAME', config('app.name'));
        define('SYS_URL', config('app.url'));
        define('SYS_URL_API', config('app.url_api'));
    }

    private $payApi = null;

    public function goPay($sp62e4cd, $sp592eb0, $sp1ad285, $sp9a29e9, $spa5c20b)
    {
        try {
            $spc03b6e           = json_decode($sp9a29e9->config, true);
            $spc03b6e['payway'] = $sp9a29e9->way;
            GatewayPay::getDriver($sp9a29e9)->goPay($spc03b6e, $sp592eb0, $sp1ad285, $sp1ad285, $spa5c20b);

            return self::renderResultPage($sp62e4cd, [
                'success' => false,
                'title'   => trans('shop.please_wait'),
                'msg'     => trans('shop.please_wait_for_pay'),
            ]);
        } catch (\Exception $spc22b6c) {
            if (config('app.debug')) {
                return self::renderResultPage($sp62e4cd, [
                    'msg' => $spc22b6c->getMessage().'<br>'.str_replace('
', '<br>', $spc22b6c->getTraceAsString()),
                ]);
            }

            return self::renderResultPage($sp62e4cd, ['msg' => $spc22b6c->getMessage()]);
        }
    }

    function buy(Request $sp62e4cd)
    {
        $sp02ae43 = $sp62e4cd->input('customer');
        if (strlen($sp02ae43) !== 32) {
            return self::renderResultPage($sp62e4cd, [
                'msg' => '提交超时，请刷新购买页面并重新提交<br><br>
当前网址: '.$sp62e4cd->getQueryString().'
提交内容: '.var_export($sp02ae43).', 提交长度:'.strlen($sp02ae43).'<br>
若您刷新后仍然出现此问题. 请加网站客服反馈',
            ]);
        }
        if (System::_getInt('vcode_shop_buy') === 1) {
            try {
                $this->validateCaptcha($sp62e4cd);
            } catch (\Throwable $spc22b6c) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('validation.captcha')]);
            }
        }
        $sp93712d = (int)$sp62e4cd->input('category_id');
        $sp5eed44 = (int)$sp62e4cd->input('product_id');
        $sp951cdc = (int)$sp62e4cd->input('count');
        $sp0c6f4c = $sp62e4cd->input('coupon');
        $sp0e200d = $sp62e4cd->input('contact');
        $spc86f0f = $sp62e4cd->input('contact_ext') ?? null;
        $spde65f9 = !empty(@json_decode($spc86f0f, true)['_mobile']);
        $sp9208ec = (int)$sp62e4cd->input('pay_id');
        if (!$sp93712d || !$sp5eed44) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.required')]);
        }
        if (strlen($sp0e200d) < 1) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.contact.required')]);
        }
        $sp0f0e2a = null;
        if (System::_getInt('order_query_password_open')) {
            $sp0f0e2a = $sp62e4cd->input('query_password');
            if (strlen($sp0f0e2a) < 1) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.query_password.required')]);
            }
            if (strlen($sp0f0e2a) < 6 || Helper::isWakePassword($sp0f0e2a)) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.query_password.weak')]);
            }
        }
        $sp1b7790 = Category::findOrFail($sp93712d);
        $spfd49bd = Product::where('id', $sp5eed44)->where('category_id', $sp93712d)->where('enabled', 1)
            ->with(['user'])->first();
        if ($spfd49bd == null || $spfd49bd->user == null) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.not_found')]);
        }
        if (!$spfd49bd->enabled) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.not_on_sell')]);
        }
        if ($spfd49bd->password_open) {
            if ($spfd49bd->password !== $sp62e4cd->input('product_password')) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.password_error')]);
            }
        } else {
            if ($sp1b7790->password_open) {
                if ($sp1b7790->password !== $sp62e4cd->input('category_password')) {
                    if ($sp1b7790->getTmpPassword() !== $sp62e4cd->input('category_password')) {
                        return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.category.password_error')]);
                    }
                }
            }
        }
        if ($sp951cdc < $spfd49bd->buy_min) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.buy_min', ['num' => $spfd49bd->buy_min])]);
        }
        if ($sp951cdc > $spfd49bd->buy_max) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.buy_max', ['num' => $spfd49bd->buy_max])]);
        }
        if ($spfd49bd->count < $sp951cdc) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.out_of_stock')]);
        }
        $sp9a29e9 = \App\Pay::find($sp9208ec);
        if ($sp9a29e9 == null || !$sp9a29e9->enabled) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.pay.not_found')]);
        }
        $sp8d2b71 = $spfd49bd->price;
        if ($spfd49bd->price_whole) {
            $spe9a4d6 = json_decode($spfd49bd->price_whole, true);
            for ($sp558f52 = count($spe9a4d6) - 1; $sp558f52 >= 0; $sp558f52--) {
                if ($sp951cdc >= (int)$spe9a4d6[$sp558f52][0]) {
                    $sp8d2b71 = (int)$spe9a4d6[$sp558f52][1];
                    break;
                }
            }
        }
        $sp1d9b51 = $sp951cdc * $sp8d2b71;
        $spa5c20b = $sp1d9b51;
        $sp923844 = 0;
        $sp0edc0d = null;
        if ($spfd49bd->support_coupon && strlen($sp0c6f4c) > 0) {
            $sp6e252b = \App\Coupon::where('user_id', $spfd49bd->user_id)->where('coupon', $sp0c6f4c)
                ->where('expire_at', '>', Carbon::now())->whereRaw('`count_used`<`count_all`')->get();
            foreach ($sp6e252b as $spf79675) {
                if (
                    $spf79675->category_id === -1
                    || $spf79675->category_id === $sp93712d
                       && ($spf79675->product_id === -1 || $spf79675->product_id === $sp5eed44)) {
                    if (
                        $spf79675->discount_type === \App\Coupon::DISCOUNT_TYPE_AMOUNT
                        && $spa5c20b >= $spf79675->discount_val) {
                        $sp0edc0d = $spf79675;
                        $sp923844 = $spf79675->discount_val;
                        break;
                    }
                    if ($spf79675->discount_type === \App\Coupon::DISCOUNT_TYPE_PERCENT) {
                        $sp0edc0d = $spf79675;
                        $sp923844 = (int)round($spa5c20b * $spf79675->discount_val / 100);
                        break;
                    }
                }
            }
            if ($sp0edc0d === null) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.coupon.invalid')]);
            }
            $spa5c20b -= $sp923844;
        }
        $sp33def5 = (int)round($spa5c20b * $sp9a29e9->fee_system);
        $speb719c = $spa5c20b - $sp33def5;
        $sp87b77b = $spde65f9 ? System::_getInt('sms_price', 10) : 0;
        $spa5c20b += $sp87b77b;
        $sp56f888 = $sp951cdc * $spfd49bd->cost;
        $sp592eb0 = \App\Order::unique_no();
        try {
            DB::transaction(function () use ($spfd49bd, $sp592eb0, $sp0edc0d, $sp0e200d, $spc86f0f, $sp0f0e2a, $sp02ae43, $sp951cdc, $sp56f888, $sp1d9b51, $sp87b77b, $sp923844, $spa5c20b, $sp9a29e9, $sp33def5, $speb719c) {
                if ($sp0edc0d) {
                    $sp0edc0d->status = \App\Coupon::STATUS_USED;
                    $sp0edc0d->count_used++;
                    $sp0edc0d->save();
                    $sp484312 = '使用优惠券: '.$sp0edc0d->coupon;
                } else {
                    $sp484312 = null;
                }
                $sp63ddab = new \App\Order([
                    'user_id'        => $spfd49bd->user_id,
                    'order_no'       => $sp592eb0,
                    'product_id'     => $spfd49bd->id,
                    'product_name'   => $spfd49bd->name,
                    'count'          => $sp951cdc,
                    'ip'             => Helper::getIP(),
                    'customer'       => $sp02ae43,
                    'contact'        => $sp0e200d,
                    'contact_ext'    => $spc86f0f,
                    'query_password' => $sp0f0e2a,
                    'cost'           => $sp56f888,
                    'price'          => $sp1d9b51,
                    'sms_price'      => $sp87b77b,
                    'discount'       => $sp923844,
                    'paid'           => $spa5c20b,
                    'pay_id'         => $sp9a29e9->id,
                    'fee'            => $sp33def5,
                    'system_fee'     => $sp33def5,
                    'income'         => $speb719c,
                    'status'         => \App\Order::STATUS_UNPAY,
                    'remark'         => $sp484312,
                    'created_at'     => Carbon::now(),
                ]);
                $sp63ddab->saveOrFail();
            });
        } catch (\Throwable $spc22b6c) {
            Log::error('Shop.Pay.buy 下单失败', ['exception' => $spc22b6c]);

            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.pay.internal_error')]);
        }
        if ($spa5c20b === 0) {
            $this->shipOrder($sp62e4cd, $sp592eb0, $spa5c20b, null);

            return redirect()->away(route('pay.result', [$sp592eb0], false));
        }
        $sp1ad285 = $sp592eb0;

        return $this->goPay($sp62e4cd, $sp592eb0, $sp1ad285, $sp9a29e9, $spa5c20b);
    }

    function pay(Request $sp62e4cd, $sp592eb0)
    {
        $sp63ddab = \App\Order::whereOrderNo($sp592eb0)->first();
        if ($sp63ddab == null) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.order.not_found')]);
        }
        if ($sp63ddab->status !== \App\Order::STATUS_UNPAY) {
            return redirect('/pay/result/'.$sp592eb0);
        }
        $sp0593e1 = 'pay: '.$sp63ddab->pay_id;
        $sp9a29e9 = $sp63ddab->pay;
        if (!$sp9a29e9) {
            Log::error($sp0593e1.' cannot find Pay');

            return $this->renderResultPage($sp62e4cd, ['msg' => trans('shop.pay.not_found')]);
        }
        $sp0593e1                 .= ','.$sp9a29e9->driver;
        $spc03b6e                 = json_decode($sp9a29e9->config, true);
        $spc03b6e['payway']       = $sp9a29e9->way;
        $spc03b6e['out_trade_no'] = $sp592eb0;
        try {
            $this->payApi = GatewayPay::getDriver($sp9a29e9);
        } catch (\Exception $spc22b6c) {
            Log::error($sp0593e1.' cannot find Driver: '.$spc22b6c->getMessage());

            return $this->renderResultPage($sp62e4cd, ['msg' => trans('shop.pay.driver_not_found')]);
        }
        if (
        $this->payApi->verify($spc03b6e, function ($sp592eb0, $sp75a792, $spd09e69) use ($sp62e4cd) {
            try {
                $this->shipOrder($sp62e4cd, $sp592eb0, $sp75a792, $spd09e69);
            } catch (\Exception $spc22b6c) {
                $this->renderResultPage($sp62e4cd, ['success' => false, 'msg' => $spc22b6c->getMessage()]);
            }
        })) {
            Log::notice($sp0593e1.' already success'.'

');

            return redirect('/pay/result/'.$sp592eb0);
        }
        if ($sp63ddab->created_at < Carbon::now()->addMinutes(-System::_getInt('order_pay_timeout_minutes', 5))) {
            return $this->renderResultPage($sp62e4cd, ['msg' => trans('shop.order.expired')]);
        }
        $spfd49bd = Product::where('id', $sp63ddab->product_id)->where('enabled', 1)->first();
        if ($spfd49bd == null) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.not_on_sell')]);
        }
        $spfd49bd->setAttribute('count', count($spfd49bd->cards) ? $spfd49bd->cards[0]->count : 0);
        if ($spfd49bd->count < $sp63ddab->count) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.out_of_stock')]);
        }
        $sp1ad285 = $sp592eb0;

        return $this->goPay($sp62e4cd, $sp592eb0, $sp1ad285, $sp9a29e9, $sp63ddab->paid);
    }

    function qrcode(Request $sp62e4cd, $sp592eb0, $spbc83ad)
    {
        $sp63ddab = \App\Order::whereOrderNo($sp592eb0)->with('product')->first();
        if ($sp63ddab == null) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.order.not_found')]);
        }
        if ($sp63ddab->created_at < Carbon::now()->addMinutes(-System::_getInt('order_pay_timeout_minutes', 5))) {
            return $this->renderResultPage($sp62e4cd, ['msg' => trans('shop.order.expired')]);
        }
        if ($sp63ddab->product_id !== \App\Product::ID_API) {
            $spfd49bd = $sp63ddab->product;
            if ($spfd49bd == null) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.not_found')]);
            }
            if ($spfd49bd->count < $sp63ddab->count) {
                return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.product.out_of_stock')]);
            }
        }
        if (strpos($spbc83ad, '..')) {
            return $this->msg(trans('shop.you_are_sb'));
        }

        return view('pay/'.$spbc83ad, [
            'pay_id' => $sp63ddab->pay_id,
            'name'   => $sp63ddab->product_id === \App\Product::ID_API ? $sp63ddab->api_out_no
                : $sp63ddab->product->name.' x '.$sp63ddab->count.'件',
            'amount' => $sp63ddab->paid,
            'qrcode' => $sp62e4cd->get('url'),
            'id'     => $sp592eb0,
        ]);
    }

    function qrQuery(Request $sp62e4cd, $sp9208ec)
    {
        $sp52477b = $sp62e4cd->input('id');
        if (isset($sp52477b[5])) {
            return self::payReturn($sp62e4cd, $sp9208ec, $sp52477b);
        } else {
            return Response::fail('order_no error');
        }
    }

    function payReturn(Request $sp62e4cd, $sp9208ec, $sp592eb0 = null)
    {
        $sp0593e1 = 'payReturn: '.$sp9208ec;
        Log::debug($sp0593e1);
        $sp9a29e9 = \App\Pay::where('id', $sp9208ec)->first();
        if (!$sp9a29e9) {
            return $this->renderResultPage($sp62e4cd, ['success' => 0, 'msg' => trans('shop.pay.not_found')]);
        }
        $sp0593e1 .= ','.$sp9a29e9->driver;
        if ($sp592eb0 && isset($sp592eb0[5])) {
            $sp63ddab = \App\Order::whereOrderNo($sp592eb0)->firstOrFail();
            if (
                $sp63ddab
                && ($sp63ddab->status === \App\Order::STATUS_PAID
                    || $sp63ddab->status === \App\Order::STATUS_SUCCESS)) {
                Log::notice($sp0593e1.' already success'.'

');
                if ($sp62e4cd->ajax()) {
                    return self::renderResultPage($sp62e4cd, [
                        'success' => 1,
                        'data'    => '/pay/result/'.$sp592eb0,
                    ], ['order' => $sp63ddab]);
                } else {
                    return redirect('/pay/result/'.$sp592eb0);
                }
            }
        }
        try {
            $this->payApi = GatewayPay::getDriver($sp9a29e9);
        } catch (\Exception $spc22b6c) {
            Log::error($sp0593e1.' cannot find Driver: '.$spc22b6c->getMessage());

            return $this->renderResultPage($sp62e4cd, ['success' => 0, 'msg' => trans('shop.pay.driver_not_found')]);
        }
        $spc03b6e                 = json_decode($sp9a29e9->config, true);
        $spc03b6e['out_trade_no'] = $sp592eb0;
        $spc03b6e['payway']       = $sp9a29e9->way;
        Log::debug($sp0593e1.' will verify');
        if (
        $this->payApi->verify($spc03b6e, function ($sp902efb, $sp75a792, $spd09e69) use ($sp62e4cd, $sp0593e1, &$sp592eb0) {
            $sp592eb0 = $sp902efb;
            try {
                Log::debug($sp0593e1
                           ." shipOrder start, order_no: {$sp592eb0}, amount: {$sp75a792}, trade_no: {$spd09e69}");
                $this->shipOrder($sp62e4cd, $sp592eb0, $sp75a792, $spd09e69);
                Log::debug($sp0593e1.' shipOrder end, order_no: '.$sp592eb0);
            } catch (\Exception $spc22b6c) {
                Log::error($sp0593e1.' shipOrder Exception: '.$spc22b6c->getMessage(), ['exception' => $spc22b6c]);
            }
        })) {
            Log::debug($sp0593e1.' verify finished: 1'.'

');
            if ($sp62e4cd->ajax()) {
                return self::renderResultPage($sp62e4cd, ['success' => 1, 'data' => '/pay/result/'.$sp592eb0]);
            } else {
                return redirect('/pay/result/'.$sp592eb0);
            }
        } else {
            Log::debug($sp0593e1.' verify finished: 0'.'

');

            return $this->renderResultPage($sp62e4cd, ['success' => 0, 'msg' => trans('shop.pay.verify_failed')]);
        }
    }

    function payNotify(Request $sp62e4cd, $sp9208ec)
    {
        $sp0593e1 = 'payNotify pay_id: '.$sp9208ec;
        Log::debug($sp0593e1);
        $sp9a29e9 = \App\Pay::where('id', $sp9208ec)->first();
        if (!$sp9a29e9) {
            Log::error($sp0593e1.' cannot find PayModel');
            echo 'fail';
            die;
        }
        $sp0593e1 .= ','.$sp9a29e9->driver;
        try {
            $this->payApi = GatewayPay::getDriver($sp9a29e9);
        } catch (\Exception $spc22b6c) {
            Log::error($sp0593e1.' cannot find Driver: '.$spc22b6c->getMessage());
            echo 'fail';
            die;
        }
        $spc03b6e             = json_decode($sp9a29e9->config, true);
        $spc03b6e['payway']   = $sp9a29e9->way;
        $spc03b6e['isNotify'] = true;
        Log::debug($sp0593e1.' will verify');
        $spa59707 = $this->payApi->verify($spc03b6e, function ($sp592eb0, $sp75a792, $spd09e69) use ($sp62e4cd, $sp0593e1) {
            try {
                Log::debug($sp0593e1
                           ." shipOrder start, order_no: {$sp592eb0}, amount: {$sp75a792}, trade_no: {$spd09e69}");
                $this->shipOrder($sp62e4cd, $sp592eb0, $sp75a792, $spd09e69);
                Log::debug($sp0593e1.' shipOrder end, order_no: '.$sp592eb0);
            } catch (\Exception $spc22b6c) {
                Log::error($sp0593e1.' shipOrder Exception: '.$spc22b6c->getMessage());
            }
        });
        Log::debug($sp0593e1.' notify finished: '.(int)$spa59707.'

');
        die;
    }

    function result(Request $sp62e4cd, $sp592eb0)
    {
        $sp63ddab = \App\Order::where('order_no', $sp592eb0)->first();
        if ($sp63ddab == null) {
            return self::renderResultPage($sp62e4cd, ['msg' => trans('shop.order.not_found')]);
        }
        if ($sp63ddab->status === \App\Order::STATUS_PAID) {
            $spffea23 = $sp63ddab->user->qq;
            if ($sp63ddab->product) {
                if ($sp63ddab->product->delivery === \App\Product::DELIVERY_MANUAL) {
                    $spfb9499 = trans('shop.order.msg_product_manual_please_wait');
                } else {
                    $spfb9499 = trans('shop.order.msg_product_out_of_stock_not_send');
                }
            } else {
                $spfb9499 = trans('shop.order.msg_product_deleted');
            }
            if ($spffea23) {
                $spfb9499 .= '<br><a href="http://wpa.qq.com/msgrd?v=3&uin='.$spffea23
                             .'&site=qq&menu=yes" target="_blank">客服QQ:'.$spffea23.'</a>';
            }

            return self::renderResultPage($sp62e4cd, [
                'success' => false,
                'title'   => trans('shop.order_is_paid'),
                'msg'     => $spfb9499,
            ], ['order' => $sp63ddab]);
        } elseif ($sp63ddab->status >= \App\Order::STATUS_SUCCESS) {
            return self::showOrderResult($sp62e4cd, $sp63ddab);
        }

        return self::renderResultPage($sp62e4cd, [
            'success' => false,
            'msg'     => $sp63ddab->remark ? trans('shop.order_process_failed_because', ['reason' => $sp63ddab->remark])
                : trans('shop.order_process_failed_default'),
        ], ['order' => $sp63ddab]);
    }

    function renderResultPage(Request $sp62e4cd, $spae8ac8, $sp41cba9 = [])
    {
        if ($sp62e4cd->ajax()) {
            if (@$spae8ac8['success']) {
                return Response::success($spae8ac8['data']);
            } else {
                return Response::fail('error', $spae8ac8['msg']);
            }
        } else {
            return view('pay.result', array_merge(['result' => $spae8ac8, 'data' => $sp41cba9], $sp41cba9));
        }
    }

    function shipOrder($sp62e4cd, $sp592eb0, $sp75a792, $spd09e69)
    {
        $sp63ddab = \App\Order::whereOrderNo($sp592eb0)->first();
        if ($sp63ddab === null) {
            Log::error('shipOrder: No query results for model [App\\Order:'.$sp592eb0.',trade_no:'.$spd09e69.',amount:'
                       .$sp75a792.']. die(\'success\');');
            die('success');
        }
        if ($sp63ddab->paid > $sp75a792) {
            Log::alert('shipOrder, price may error, order_no:'.$sp592eb0.', paid:'.$sp63ddab->paid.', $amount get:'
                       .$sp75a792);
            $sp63ddab->remark = '支付金额('.sprintf('%0.2f', $sp75a792 / 100).') 小于 订单金额('.sprintf('%0.2f', $sp63ddab->paid
                                                                                                        / 100).')';
            $sp63ddab->save();
            throw new \Exception($sp63ddab->remark);
        }
        $spfd49bd = null;
        if ($sp63ddab->status === \App\Order::STATUS_UNPAY) {
            Log::debug('shipOrder.first_process:'.$sp592eb0);
            if (
            FundHelper::orderSuccess($sp63ddab->id, function ($spa5bcc7) use ($spd09e69, &$sp63ddab, &$spfd49bd) {
                $sp63ddab = $spa5bcc7;
                if ($sp63ddab->status !== \App\Order::STATUS_UNPAY) {
                    Log::debug('Shop.Pay.shipOrder: .first_process:'.$sp63ddab->order_no.' already processed! #2');

                    return false;
                }
                $spfd49bd               = $sp63ddab->product()->lockForUpdate()->firstOrFail();
                $sp63ddab->pay_trade_no = $spd09e69;
                $sp63ddab->paid_at      = Carbon::now();
                if ($spfd49bd->delivery === \App\Product::DELIVERY_MANUAL) {
                    $sp63ddab->status      = \App\Order::STATUS_PAID;
                    $sp63ddab->send_status = \App\Order::SEND_STATUS_CARD_UN;
                    $sp63ddab->saveOrFail();

                    return true;
                }
                if ($spfd49bd->delivery === \App\Product::DELIVERY_API) {
                    $spb533ba = $spfd49bd->createApiCards($sp63ddab);
                } else {
                    $spb533ba = Card::where('product_id', $sp63ddab->product_id)->whereRaw('`count_sold`<`count_all`')
                        ->take($sp63ddab->count)->lockForUpdate()->get();
                }
                $sp4571b3 = false;
                if (count($spb533ba) === $sp63ddab->count) {
                    $sp4571b3 = true;
                } else {
                    if (count($spb533ba)) {
                        foreach ($spb533ba as $spb6adc1) {
                            if ($spb6adc1->type === \App\Card::TYPE_REPEAT && $spb6adc1->count >= $sp63ddab->count) {
                                $spb533ba = [$spb6adc1];
                                $sp4571b3 = true;
                                break;
                            }
                        }
                    }
                }
                if ($sp4571b3 === false) {
                    Log::alert('Shop.Pay.shipOrder: 订单:'.$sp63ddab->order_no.', 购买数量:'.$sp63ddab->count.', 卡数量:'
                               .count($spb533ba).' 卡密不足(已支付 未发货)');
                    $sp63ddab->status = \App\Order::STATUS_PAID;
                    $sp63ddab->saveOrFail();

                    return true;
                } else {
                    $sp43c46a = [];
                    foreach ($spb533ba as $spb6adc1) {
                        $sp43c46a[] = $spb6adc1->id;
                    }
                    $sp63ddab->cards()->attach($sp43c46a);
                    if (count($spb533ba) === 1 && $spb533ba[0]->type === \App\Card::TYPE_REPEAT) {
                        \App\Card::where('id', $sp43c46a[0])->update([
                            'status'     => \App\Card::STATUS_SOLD,
                            'count_sold' => DB::raw('`count_sold`+'.$sp63ddab->count),
                        ]);
                    } else {
                        \App\Card::whereIn('id', $sp43c46a)->update([
                            'status'     => \App\Card::STATUS_SOLD,
                            'count_sold' => DB::raw('`count_sold`+1'),
                        ]);
                    }
                    $sp63ddab->status = \App\Order::STATUS_SUCCESS;
                    $sp63ddab->saveOrFail();
                    $spfd49bd->count_sold += $sp63ddab->count;
                    $spfd49bd->saveOrFail();

                    return FundHelper::ACTION_CONTINUE;
                }
            })) {
                if ($spfd49bd->count_warn > 0 && $spfd49bd->count < $spfd49bd->count_warn) {
                    try {
                        Mail::to($sp63ddab->user->email)->Queue(new ProductCountWarn($spfd49bd, $spfd49bd->count));
                    } catch (\Throwable $spc22b6c) {
                        LogHelper::setLogFile('mail');
                        Log::error('shipOrder.count_warn error', [
                            'product_id' => $sp63ddab->product_id,
                            'email'      => $sp63ddab->user->email,
                            'exception'  => $spc22b6c->getMessage(),
                        ]);
                        LogHelper::setLogFile('card');
                    }
                }
                if (System::_getInt('mail_send_order')) {
                    $spa6cdee = @json_decode($sp63ddab->contact_ext, true)['_mail'];
                    if ($spa6cdee) {
                        $sp63ddab->sendEmail($spa6cdee);
                    }
                }
                if ($sp63ddab->status === \App\Order::STATUS_SUCCESS && System::_getInt('sms_send_order')) {
                    $sp75a7e2 = @json_decode($sp63ddab->contact_ext, true)['_mobile'];
                    if ($sp75a7e2) {
                        $sp63ddab->sendSms($sp75a7e2);
                    }
                }
            } else {
                if ($sp63ddab->status !== \App\Order::STATUS_UNPAY) {
                } else {
                    Log::error('Pay.shipOrder.orderSuccess Failed.');

                    return false;
                }
            }
        } else {
            Log::debug('Shop.Pay.shipOrder: .order_no:'.$sp63ddab->order_no.' already processed! #1');
        }

        return false;
    }

    private function showOrderResult($sp62e4cd, $sp63ddab)
    {
        return self::renderResultPage($sp62e4cd, [
            'success' => true,
            'msg'     => $sp63ddab->getSendMessage(),
        ], [
            'card_txt' => join('&#013;&#010;', $sp63ddab->getCardsArray()),
            'order'    => $sp63ddab,
            'product'  => $sp63ddab->product,
        ]);
    }
}
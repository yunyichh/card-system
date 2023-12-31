<?php

namespace App;

use App\Jobs\OrderSms;
use App\Library\LogHelper;
use App\Mail\OrderShipped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log as LogWriter;

class Order extends Model
{
    protected $guarded = [];
    const STATUS_UNPAY                = 0;
    const STATUS_PAID                 = 1;
    const STATUS_SUCCESS              = 2;
    const STATUS_FROZEN               = 3;
    const STATUS_REFUND               = 4;
    const STATUS                      = [0 => '未支付', 1 => '未发货', 2 => '已发货', 3 => '已冻结', 4 => '已退款'];
    const SEND_STATUS_UN              = 0;
    const SEND_STATUS_EMAIL_SUCCESS   = 1;
    const SEND_STATUS_EMAIL_FAILED    = 2;
    const SEND_STATUS_MOBILE_SUCCESS  = 3;
    const SEND_STATUS_MOBILE_FAILED   = 4;
    const SEND_STATUS_CARD_UN         = 100;
    const SEND_STATUS_CARD_PROCESSING = 101;
    const SEND_STATUS_CARD_SUCCESS    = 102;
    const SEND_STATUS_CARD_FAILED     = 103;
    protected $casts = ['api_info' => 'array'];

    public static function unique_no()
    {
        $sp592eb0 = date('YmdHis').str_random(5);
        while (\App\Order::where('order_no', $sp592eb0)->exists()) {
            $sp592eb0 = date('YmdHis').str_random(5);
        }

        return $sp592eb0;
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function product()
    {
        return $this->belongsTo(Product::class);
    }

    function pay()
    {
        return $this->belongsTo(Pay::class);
    }

    function cards()
    {
        $spff6859 = $this->belongsToMany(Card::class);

        return $spff6859->withTrashed();
    }

    function card_orders()
    {
        return $this->hasMany(CardOrder::class);
    }

    function fundRecord()
    {
        return $this->hasMany(FundRecord::class);
    }

    function getCardsArray()
    {
        $spb533ba = [];
        $this->cards->each(function ($spb6adc1) use (&$spb533ba) {
            $spb533ba[] = $spb6adc1->card;
        });

        return $spb533ba;
    }

    function getSendMessage()
    {
        if (count($this->cards)) {
            if (count($this->cards) == $this->count) {
                $sp62536c = '订单#'.$this->order_no.'&nbsp;已支付，您购买的内容如下：';
            } else {
                if (
                    $this->cards[0]->type === \App\Card::TYPE_REPEAT
                    || @$this->product->delivery === \App\Product::DELIVERY_MANUAL) {
                    $sp62536c = '订单#'.$this->order_no.'&nbsp;已支付，您购买的内容如下：';
                } else {
                    $sp62536c = '订单#'.$this->order_no.'&nbsp;已支付，目前库存不足，您还有'.($this->count - count($this->cards))
                                .'件未发货，请联系商家客服发货，';
                    $sp62536c .= '商家客服QQ：<a href="http://wpa.qq.com/msgrd?v=3&uin='.$this->user->qq
                                 .'&site=qq&menu=yes" target="_blank">'.$this->user->qq.'</a><br>';
                    $sp62536c .= '已发货商品见下方：';
                }
            }
        } else {
            $sp62536c = '订单#'.$this->order_no.'&nbsp;已支付，目前库存不足，您购买的'.($this->count - count($this->cards))
                        .'件未发货，请联系商家客服发货<br>';
            $sp62536c .= '商家客服QQ：<a href="http://wpa.qq.com/msgrd?v=3&uin='.$this->user->qq
                         .'&site=qq&menu=yes" target="_blank">'.$this->user->qq.'</a>';
        }

        return $sp62536c;
    }

    function sendEmail($spa6cdee = false)
    {
        if ($spa6cdee === false) {
            $spa6cdee = @json_decode($this->contact_ext)['_mail'];
        }
        if (!$spa6cdee || !@filter_var($spa6cdee, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $spb533ba = $this->getCardsArray();
        try {
            Mail::to($spa6cdee)->Queue(new OrderShipped($this, $this->getSendMessage(), join('<br>', $spb533ba)));
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_SUCCESS;
            $this->saveOrFail();
        } catch (\Throwable $spc22b6c) {
            $this->send_status = \App\Order::SEND_STATUS_EMAIL_FAILED;
            $this->saveOrFail();
            LogHelper::setLogFile('mail');
            LogWriter::error('Order.sendEmail error', [
                'order_no'  => $this->order_no,
                'email'     => $spa6cdee,
                'cards'     => $spb533ba,
                'exception' => $spc22b6c->getMessage(),
            ]);
            LogHelper::setLogFile('card');
        }
    }

    function sendSms($sp75a7e2 = false)
    {
        if ($sp75a7e2 === false) {
            $sp75a7e2 = @json_decode($this->contact_ext)['_mobile'];
        }
        if (!$sp75a7e2 || strlen($sp75a7e2) !== 11) {
            return;
        }
        OrderSms::dispatch($sp75a7e2, $this);
    }
}
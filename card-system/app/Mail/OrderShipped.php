<?php
namespace App\Mail; use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable; use Illuminate\Queue\SerializesModels; use Illuminate\Contracts\Queue\ShouldQueue; class OrderShipped extends Mailable { use Queueable, SerializesModels; public $tries = 3; public $timeout = 20; public $order_no; public $order_url; public $card_msg; public $cards_txt; public function __construct($sp63ddab, $sp62536c, $sp148704) { $this->order_no = $sp63ddab->order_no; $this->order_url = config('app.url') . route('pay.result', array($sp63ddab->order_no), false); $this->card_msg = $sp62536c; $this->cards_txt = $sp148704; } public function build() { return $this->subject('订单提醒(#' . $this->order_no . ')-' . config('app.name'))->view('emails.order'); } }
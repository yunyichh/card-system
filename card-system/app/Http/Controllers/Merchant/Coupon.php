<?php
namespace App\Http\Controllers\Merchant; use App\Library\Response; use Carbon\Carbon; use Illuminate\Http\Request; use App\Http\Controllers\Controller; class Coupon extends Controller { function get(Request $sp62e4cd) { $sp4210ad = $this->authQuery($sp62e4cd, \App\Coupon::class)->with(array('category' => function ($sp4210ad) { $sp4210ad->select(array('id', 'name')); }))->with(array('product' => function ($sp4210ad) { $sp4210ad->select(array('id', 'name')); })); $spe0aaed = $sp62e4cd->input('search', false); $sp75c4d8 = $sp62e4cd->input('val', false); if ($spe0aaed && $sp75c4d8) { if ($spe0aaed == 'id') { $sp4210ad->where('id', $sp75c4d8); } else { $sp4210ad->where($spe0aaed, 'like', '%' . $sp75c4d8 . '%'); } } $sp93712d = (int) $sp62e4cd->input('category_id'); $sp5eed44 = $sp62e4cd->input('product_id', -1); if ($sp93712d > 0) { if ($sp5eed44 > 0) { $sp4210ad->where('product_id', $sp5eed44); } else { $sp4210ad->where('category_id', $sp93712d); } } $sp7acd11 = $sp62e4cd->input('status'); if (strlen($sp7acd11)) { $sp4210ad->whereIn('status', explode(',', $sp7acd11)); } $spa8b0dd = $sp62e4cd->input('type'); if (strlen($spa8b0dd)) { $sp4210ad->whereIn('type', explode(',', $spa8b0dd)); } $sp4210ad->orderByRaw('expire_at DESC,category_id,product_id,type,status'); $sp295466 = (int) $sp62e4cd->input('current_page', 1); $spe5b040 = (int) $sp62e4cd->input('per_page', 20); $sp6492f8 = $sp4210ad->paginate($spe5b040, array('*'), 'page', $sp295466); return Response::success($sp6492f8); } function create(Request $sp62e4cd) { $sp951cdc = $sp62e4cd->post('count', 0); $spa8b0dd = (int) $sp62e4cd->post('type', \App\Coupon::TYPE_ONETIME); $spb1f599 = $sp62e4cd->post('expire_at'); $spf6998c = (int) $sp62e4cd->post('discount_val'); $sp43902b = (int) $sp62e4cd->post('discount_type', \App\Coupon::DISCOUNT_TYPE_AMOUNT); $sp484312 = $sp62e4cd->post('remark'); if ($sp43902b === \App\Coupon::DISCOUNT_TYPE_AMOUNT) { if ($spf6998c < 1 || $spf6998c > 1000000000) { return Response::fail('优惠券面额需要在0.01-10000000之间'); } } if ($sp43902b === \App\Coupon::DISCOUNT_TYPE_PERCENT) { if ($spf6998c < 1 || $spf6998c > 100) { return Response::fail('优惠券面额需要在1-100之间'); } } $sp93712d = (int) $sp62e4cd->post('category_id', -1); $sp5eed44 = (int) $sp62e4cd->post('product_id', -1); if ($spa8b0dd === \App\Coupon::TYPE_REPEAT) { $sp0c6f4c = $sp62e4cd->post('coupon'); if (!$sp0c6f4c) { $sp0c6f4c = strtoupper(str_random()); } $spf79675 = new \App\Coupon(); $spf79675->user_id = $this->getUserIdOrFail($sp62e4cd); $spf79675->category_id = $sp93712d; $spf79675->product_id = $sp5eed44; $spf79675->coupon = $sp0c6f4c; $spf79675->type = $spa8b0dd; $spf79675->discount_val = $spf6998c; $spf79675->discount_type = $sp43902b; $spf79675->count_all = (int) $sp62e4cd->post('count_all', 1); if ($spf79675->count_all < 1 || $spf79675->count_all > 10000000) { return Response::fail('可用次数不能超过10000000'); } $spf79675->expire_at = $spb1f599; $spf79675->saveOrFail(); return Response::success(array($spf79675->coupon)); } elseif ($spa8b0dd === \App\Coupon::TYPE_ONETIME) { if (!$sp951cdc) { return Response::forbidden('请输入生成数量'); } if ($sp951cdc > 100) { return Response::forbidden('每次生成不能大于100张'); } $sp6e252b = array(); $sp0a5870 = array(); $spe0b9a0 = $this->getUserIdOrFail($sp62e4cd); $spaa3999 = Carbon::now(); for ($sp558f52 = 0; $sp558f52 < $sp951cdc; $sp558f52++) { $spf79675 = strtoupper(str_random()); $sp0a5870[] = $spf79675; $sp6e252b[] = array('user_id' => $spe0b9a0, 'coupon' => $spf79675, 'category_id' => $sp93712d, 'product_id' => $sp5eed44, 'type' => $spa8b0dd, 'discount_val' => $spf6998c, 'discount_type' => $sp43902b, 'status' => \App\Coupon::STATUS_NORMAL, 'remark' => $sp484312, 'created_at' => $spaa3999, 'expire_at' => $spb1f599); } \App\Coupon::insert($sp6e252b); return Response::success($sp0a5870); } else { return Response::forbidden('unknown type: ' . $spa8b0dd); } } function edit(Request $sp62e4cd) { $spdc31ea = (int) $sp62e4cd->post('id'); $sp0c6f4c = $sp62e4cd->post('coupon'); $sp93712d = (int) $sp62e4cd->post('category_id', -1); $sp5eed44 = (int) $sp62e4cd->post('product_id', -1); $spb1f599 = $sp62e4cd->post('expire_at', NULL); $sp7acd11 = (int) $sp62e4cd->post('status', \App\Coupon::STATUS_NORMAL); $spa8b0dd = (int) $sp62e4cd->post('type', \App\Coupon::TYPE_ONETIME); $spf6998c = (int) $sp62e4cd->post('discount_val'); $sp43902b = (int) $sp62e4cd->post('discount_type', \App\Coupon::DISCOUNT_TYPE_AMOUNT); if ($sp43902b === \App\Coupon::DISCOUNT_TYPE_AMOUNT) { if ($spf6998c < 1 || $spf6998c > 1000000000) { return Response::fail('优惠券面额需要在0.01-10000000之间'); } } if ($sp43902b === \App\Coupon::DISCOUNT_TYPE_PERCENT) { if ($spf6998c < 1 || $spf6998c > 100) { return Response::fail('优惠券面额需要在1-100之间'); } } $spf79675 = $this->authQuery($sp62e4cd, \App\Coupon::class)->find($spdc31ea); if ($spf79675) { $spf79675->coupon = $sp0c6f4c; $spf79675->category_id = $sp93712d; $spf79675->product_id = $sp5eed44; $spf79675->status = $sp7acd11; $spf79675->type = $spa8b0dd; $spf79675->discount_val = $spf6998c; $spf79675->discount_type = $sp43902b; if ($spa8b0dd === \App\Coupon::TYPE_REPEAT) { $spf79675->count_all = (int) $sp62e4cd->post('count_all', 1); if ($spf79675->count_all < 1 || $spf79675->count_all > 10000000) { return Response::fail('可用次数不能超过10000000'); } } if ($spb1f599) { $spf79675->expire_at = $spb1f599; } $spf79675->saveOrFail(); } else { $sp70c99f = explode('
', $sp0c6f4c); for ($sp558f52 = 0; $sp558f52 < count($sp70c99f); $sp558f52++) { $sp839043 = str_replace('
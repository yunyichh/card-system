<?php

namespace App\Http\Controllers\Shop;

use App\Category;
use App\Product;
use App\Library\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Coupon extends Controller
{
    function info(Request $sp62e4cd)
    {
        $sp93712d = (int)$sp62e4cd->post('category_id', -1);
        $sp5eed44 = (int)$sp62e4cd->post('product_id', -1);
        $sp0c6f4c = $sp62e4cd->post('coupon');
        if (!$sp0c6f4c) {
            return Response::fail(trans('shop.coupon.required'));
        }
        if ($sp93712d > 0) {
            $sp1b7790 = Category::findOrFail($sp93712d);
            $spe0b9a0 = $sp1b7790->user_id;
        } elseif ($sp5eed44 > 0) {
            $spfd49bd = Product::findOrFail($sp5eed44);
            $spe0b9a0 = $spfd49bd->user_id;
        } else {
            return Response::fail(trans('shop.please_select_category_or_product'));
        }
        $sp6e252b = \App\Coupon::where('user_id', $spe0b9a0)->where('coupon', $sp0c6f4c)
            ->where('expire_at', '>', Carbon::now())->whereRaw('`count_used`<`count_all`')->get();
        foreach ($sp6e252b as $sp0c6f4c) {
            if (
                $sp0c6f4c->category_id === -1
                || $sp0c6f4c->category_id === $sp93712d
                   && ($sp0c6f4c->product_id === -1
                       || $sp0c6f4c->product_id === $sp5eed44)) {
                $sp0c6f4c->setVisible(['discount_type', 'discount_val']);

                return Response::success($sp0c6f4c);
            }
        }

        return Response::fail(trans('shop.coupon.invalid'));
    }
}
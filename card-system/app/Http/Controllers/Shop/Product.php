<?php

namespace App\Http\Controllers\Shop;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Response;

class Product extends Controller
{
    function get(Request $sp62e4cd)
    {
        $sp93712d = (int)$sp62e4cd->post('category_id');
        if (!$sp93712d) {
            return Response::forbidden(trans('shop.category.required'));
        }
        $sp1b7790 = \App\Category::where('id', $sp93712d)->first();
        if (!$sp1b7790) {
            return Response::forbidden(trans('shop.category.not_found'));
        }
        if ($sp1b7790->password_open && $sp62e4cd->post('password') !== $sp1b7790->password) {
            return Response::fail(trans('shop.category.password_error'));
        }
        $sp51e802 = \App\Product::where('category_id', $sp93712d)->where('enabled', 1)->orderBy('sort')->get();
        foreach ($sp51e802 as $spfd49bd) {
            $spfd49bd->setForShop();
        }

        return Response::success($sp51e802);
    }

    function verifyPassword(Request $sp62e4cd)
    {
        $sp5eed44 = (int)$sp62e4cd->post('product_id');
        if (!$sp5eed44) {
            return Response::forbidden(trans('shop.product.required'));
        }
        $spfd49bd = \App\Product::where('id', $sp5eed44)->first();
        if (!$spfd49bd) {
            return Response::forbidden(trans('shop.product.not_found'));
        }
        if ($spfd49bd->password_open && $sp62e4cd->post('password') !== $spfd49bd->password) {
            return Response::fail(trans('shop.product.password_error'));
        }

        return Response::success();
    }
}
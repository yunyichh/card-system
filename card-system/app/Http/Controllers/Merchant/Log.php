<?php
namespace App\Http\Controllers\Merchant; use App\Library\Response; use Illuminate\Http\Request; use App\Http\Controllers\Controller; use Illuminate\Support\Facades\Auth; class Log extends Controller { function get(Request $sp62e4cd) { $spe0b9a0 = $sp62e4cd->input('user_id'); $sp951406 = $sp62e4cd->input('action', \App\Log::ACTION_LOGIN); $sp4210ad = \App\Log::where('action', $sp951406); $sp4210ad->where('user_id', Auth::id()); $sp20bce8 = $sp62e4cd->input('start_at'); if (strlen($sp20bce8)) { $sp4210ad->where('created_at', '>=', $sp20bce8 . ' 00:00:00'); } $sp761637 = $sp62e4cd->input('end_at'); if (strlen($sp761637)) { $sp4210ad->where('created_at', '<=', $sp761637 . ' 23:59:59'); } $sp295466 = (int) $sp62e4cd->input('current_page', 1); $spe5b040 = (int) $sp62e4cd->input('per_page', 20); $sp6492f8 = $sp4210ad->orderBy('created_at', 'DESC')->paginate($spe5b040, array('*'), 'page', $sp295466); return Response::success($sp6492f8); } }
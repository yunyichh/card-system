<?php
namespace App\Library; use App\Order; use App\User; use App\FundRecord; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Log; class FundHelper { const ACTION_CONTINUE = 1001; public static function orderSuccess($sp885174, callable $sp892346) { $sp63ddab = null; try { return DB::transaction(function () use($sp885174, &$sp63ddab, $sp892346) { $sp63ddab = \App\Order::where('id', $sp885174)->lockForUpdate()->firstOrFail(); $spa59707 = $sp892346($sp63ddab); if ($spa59707 !== self::ACTION_CONTINUE) { return $spa59707; } $spbfa519 = User::where('id', $sp63ddab->user_id)->lockForUpdate()->firstOrFail(); $spbfa519->m_all += $sp63ddab->income; $spbfa519->saveOrFail(); $sp801211 = new FundRecord(); $sp801211->user_id = $sp63ddab->user_id; $sp801211->type = FundRecord::TYPE_IN; $sp801211->amount = $sp63ddab->income; $sp801211->all = $spbfa519->m_all; $sp801211->frozen = $spbfa519->m_frozen; $sp801211->paid = $spbfa519->m_paid; $sp801211->balance = $spbfa519->m_balance; $sp801211->remark = '订单#' . $sp63ddab->order_no; $sp801211->order_id = $sp63ddab->id; $sp801211->saveOrFail(); return true; }); } catch (\Throwable $spc22b6c) { $spfb9499 = 'FundHelper.orderSuccess error, order_id:' . $sp885174; if ($sp63ddab) { $spfb9499 .= ', user_id:' . $sp63ddab->user_id . ',income:' . $sp63ddab->income . ',order_no:' . $sp63ddab->order_no; } Log::error($spfb9499 . ' with exception:', array('Exception' => $spc22b6c)); return false; } } public static function orderFreeze($sp885174, $sp6c0c0c) { $sp63ddab = null; try { return DB::transaction(function () use($sp885174, &$sp63ddab, $sp6c0c0c) { $sp63ddab = \App\Order::where('id', $sp885174)->lockForUpdate()->firstOrFail(); if ($sp63ddab->status === Order::STATUS_REFUND) { return false; } if ($sp63ddab->status === Order::STATUS_FROZEN) { return true; } $sp86519a = $sp63ddab->status; if ($sp86519a === \App\Order::STATUS_SUCCESS) { $sp7ce71d = '已发货'; } elseif ($sp86519a === \App\Order::STATUS_UNPAY) { $sp7ce71d = '未付款'; } elseif ($sp86519a === \App\Order::STATUS_PAID) { $sp7ce71d = '未发货'; } else { throw new \Exception('unknown'); } $spbfa519 = User::where('id', $sp63ddab->user_id)->lockForUpdate()->firstOrFail(); $sp801211 = new FundRecord(); $sp801211->user_id = $sp63ddab->user_id; $sp801211->type = FundRecord::TYPE_OUT; $sp801211->order_id = $sp63ddab->id; $sp801211->remark = $sp63ddab === $sp63ddab ? '' : '关联订单#' . $sp63ddab->order_no . ': '; if ($sp86519a === \App\Order::STATUS_SUCCESS) { $spbfa519->m_frozen += $sp63ddab->income; $spbfa519->saveOrFail(); $sp801211->amount = -$sp63ddab->income; $sp801211->remark .= $sp6c0c0c . ', 冻结订单#' . $sp63ddab->order_no; } else { $sp801211->amount = 0; $sp801211->remark .= $sp6c0c0c . ', 冻结订单(' . $sp7ce71d . ')#' . $sp63ddab->order_no; } $sp801211->all = $spbfa519->m_all; $sp801211->frozen = $spbfa519->m_frozen; $sp801211->paid = $spbfa519->m_paid; $sp801211->balance = $spbfa519->m_balance; $sp801211->saveOrFail(); $sp63ddab->status = \App\Order::STATUS_FROZEN; $sp63ddab->frozen_reason = ($sp63ddab === $sp63ddab ? '' : '关联订单#' . $sp63ddab->order_no . ': ') . $sp6c0c0c; $sp63ddab->saveOrFail(); return true; }); } catch (\Throwable $spc22b6c) { $spfb9499 = 'FundHelper.orderFreeze error'; if ($sp63ddab) { $spfb9499 .= ', order_no:' . $sp63ddab->order_no . ', user_id:' . $sp63ddab->user_id . ', amount:' . $sp63ddab->income; } else { $spfb9499 .= ', order_no: null'; } Log::error($spfb9499 . ' with exception:', array('Exception' => $spc22b6c)); return false; } } public static function orderUnfreeze($sp885174, $sp63f8e4, callable $sp2d7864 = null, &$sp826c98 = null) { $sp63ddab = null; try { return DB::transaction(function () use($sp885174, &$sp63ddab, $sp63f8e4, $sp2d7864, &$sp826c98) { $sp63ddab = \App\Order::where('id', $sp885174)->lockForUpdate()->firstOrFail(); if ($sp2d7864 !== null) { $spa59707 = $sp2d7864(); if ($spa59707 !== self::ACTION_CONTINUE) { return $spa59707; } } if ($sp63ddab->status === Order::STATUS_REFUND) { $sp826c98 = $sp63ddab->status; return false; } if ($sp63ddab->status !== Order::STATUS_FROZEN) { $sp826c98 = $sp63ddab->status; return true; } $sp6ef70f = $sp63ddab->card_orders()->exists(); if ($sp6ef70f) { $sp826c98 = \App\Order::STATUS_SUCCESS; $sp7ce71d = '已发货'; } else { if ($sp63ddab->paid_at === NULL) { $sp826c98 = \App\Order::STATUS_UNPAY; $sp7ce71d = '未付款'; } else { $sp826c98 = \App\Order::STATUS_PAID; $sp7ce71d = '未发货'; } } $spbfa519 = User::where('id', $sp63ddab->user_id)->lockForUpdate()->firstOrFail(); $sp801211 = new FundRecord(); $sp801211->user_id = $sp63ddab->user_id; $sp801211->type = FundRecord::TYPE_IN; $sp801211->remark = $sp63ddab === $sp63ddab ? '' : '关联订单#' . $sp63ddab->order_no . ': '; $sp801211->order_id = $sp63ddab->id; if ($sp6ef70f) { $spbfa519->m_frozen -= $sp63ddab->income; $spbfa519->saveOrFail(); $sp801211->amount = $sp63ddab->income; $sp801211->remark .= $sp63f8e4 . ', 解冻订单#' . $sp63ddab->order_no; } else { $sp801211->amount = 0; $sp801211->remark .= $sp63f8e4 . ', 解冻订单(' . $sp7ce71d . ')#' . $sp63ddab->order_no; } $sp801211->all = $spbfa519->m_all; $sp801211->frozen = $spbfa519->m_frozen; $sp801211->paid = $spbfa519->m_paid; $sp801211->balance = $spbfa519->m_balance; $sp801211->saveOrFail(); $sp63ddab->status = $sp826c98; $sp63ddab->saveOrFail(); return true; }); } catch (\Throwable $spc22b6c) { $spfb9499 = 'FundHelper.orderUnfreeze error'; if ($sp63ddab) { $spfb9499 .= ', order_no:' . $sp63ddab->order_no . ', user_id:' . $sp63ddab->user_id . ',amount:' . $sp63ddab->income; } else { $spfb9499 .= ', order_no: null'; } Log::error($spfb9499 . ' with exception:', array('Exception' => $spc22b6c)); return false; } } }
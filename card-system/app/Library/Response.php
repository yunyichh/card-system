<?php

namespace App\Library;

class Response
{
    public static function json($spfe8869 = [], $sp7acd11 = 200, array $spcb575a = [], $spb6ee65 = 0)
    {
        return response()->json($spfe8869, $sp7acd11, $spcb575a, $spb6ee65);
    }

    public static function success($spfe8869 = [])
    {
        return self::json(['message' => 'success', 'data' => $spfe8869]);
    }

    public static function fail($spfb9499 = 'fail', $spfe8869 = [])
    {
        return self::json(['message' => $spfb9499, 'data' => $spfe8869], 500);
    }

    public static function forbidden($spfb9499 = 'forbidden', $spfe8869 = [])
    {
        return self::json(['message' => $spfb9499, 'data' => $spfe8869], 403);
    }
}
<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;

class CurlRequest
{
    public static function curl($spfaab7a, $sp89857c = 0, $spa939f2 = '', $spcb575a = [], $sp23ae43 = 5, &$spc94a6d = false, &$sp8eaaf0 = false, &$sp827c9d = false)
    {
        if (!isset($spcb575a['Accept']) && !isset($spcb575a['accept'])) {
            $spcb575a['Accept'] = '*/*';
        }
        if (!isset($spcb575a['Referer']) && !isset($spcb575a['referer'])) {
            $spcb575a['Referer'] = $spfaab7a;
        }
        if (!isset($spcb575a['Content-Type']) && !isset($spcb575a['content-type'])) {
            $spcb575a['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        if (!isset($spcb575a['User-Agent']) && !isset($spcb575a['user-agent'])) {
            $spcb575a['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';
        }
        if ($spc94a6d !== false) {
            $spcb575a['Cookie'] = $spc94a6d;
        }
        $sp323a76 = [];
        foreach ($spcb575a as $sp481eb4 => $sp03810b) {
            $sp323a76[] = $sp481eb4.': '.$sp03810b;
        }
        $sp323a76[] = 'Expect:';
        $sp6e82aa   = curl_init();
        curl_setopt($sp6e82aa, CURLOPT_URL, $spfaab7a);
        curl_setopt($sp6e82aa, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($sp6e82aa, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($sp6e82aa, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($sp6e82aa, CURLOPT_MAXREDIRS, 3);
        if ($sp89857c == 1) {
            curl_setopt($sp6e82aa, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($sp6e82aa, CURLOPT_POST, 1);
            if ($spa939f2 !== '') {
                curl_setopt($sp6e82aa, CURLOPT_POSTFIELDS, $spa939f2);
                curl_setopt($sp6e82aa, CURLOPT_POSTREDIR, 3);
            }
        }
        if (defined('MY_PROXY')) {
            $sp87cc24 = MY_PROXY;
            $sp1e1501 = CURLPROXY_HTTP;
            if (strpos($sp87cc24, 'http://') || strpos($sp87cc24, 'https://')) {
                $sp87cc24 = str_replace('http://', $sp87cc24, $sp87cc24);
                $sp87cc24 = str_replace('https://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_HTTP;
            } elseif (strpos($sp87cc24, 'socks4://')) {
                $sp87cc24 = str_replace('socks4://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS4;
            } elseif (strpos($sp87cc24, 'socks4a://')) {
                $sp87cc24 = str_replace('socks4a://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS4A;
            } elseif (strpos($sp87cc24, 'socks5://')) {
                $sp87cc24 = str_replace('socks5://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS5_HOSTNAME;
            }
            curl_setopt($sp6e82aa, CURLOPT_PROXY, $sp87cc24);
            curl_setopt($sp6e82aa, CURLOPT_PROXYTYPE, $sp1e1501);
            if (defined('MY_PROXY_PASS')) {
                curl_setopt($sp6e82aa, CURLOPT_PROXYUSERPWD, MY_PROXY_PASS);
            }
        }
        curl_setopt($sp6e82aa, CURLOPT_TIMEOUT, $sp23ae43);
        curl_setopt($sp6e82aa, CURLOPT_CONNECTTIMEOUT, $sp23ae43);
        curl_setopt($sp6e82aa, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sp6e82aa, CURLOPT_HEADER, 1);
        curl_setopt($sp6e82aa, CURLOPT_HTTPHEADER, $sp323a76);
        $sp3e8d87 = curl_exec($sp6e82aa);
        if (curl_errno($sp6e82aa)) {
            $sp827c9d = curl_error($sp6e82aa);
            curl_close($sp6e82aa);

            return null;
        }
        $sp9e2384 = curl_getinfo($sp6e82aa, CURLINFO_HEADER_SIZE);
        if ($sp8eaaf0) {
            $sp8eaaf0 = curl_getinfo($sp6e82aa, CURLINFO_HTTP_CODE);
        }
        $sp6b21b1 = substr($sp3e8d87, 0, $sp9e2384);
        $sp81d4e8 = substr($sp3e8d87, $sp9e2384);
        curl_close($sp6e82aa);
        if ($spc94a6d !== false) {
            $spcb575a = explode('
', $sp6b21b1);
            $sp2893fd = '';
            foreach ($spcb575a as $sp6b21b1) {
                if (strtolower(substr($sp6b21b1, 0, 11)) === 'set-cookie:') {
                    $sp6b21b1 = 'Set-Cookie:'.substr($sp6b21b1, 11);
                    if (strpos($sp6b21b1, 'Set-Cookie') !== false) {
                        if (strpos($sp6b21b1, ';') !== false) {
                            $sp2893fd = $sp2893fd.trim(Helper::str_between($sp6b21b1, 'Set-Cookie:', ';')).';';
                        } else {
                            $sp2893fd = $sp2893fd.trim(str_replace('Set-Cookie:', '', $sp6b21b1)).';';
                        }
                    }
                }
            }
            $spc94a6d = self::combineCookie($spc94a6d, $sp2893fd);
        }

        return $sp81d4e8;
    }

    public static function get($spfaab7a, $spcb575a = [], $sp23ae43 = 5, &$spc94a6d = false)
    {
        return self::curl($spfaab7a, 0, '', $spcb575a, $sp23ae43, $spc94a6d);
    }

    public static function post($spfaab7a, $spa939f2 = '', $spcb575a = [], $sp23ae43 = 5, &$spc94a6d = false)
    {
        return self::curl($spfaab7a, 1, $spa939f2, $spcb575a, $sp23ae43, $spc94a6d);
    }

    public static function download($spfaab7a, $spd9c775, $sp2b9683 = false)
    {
        $sp8e6d86 = fopen($spd9c775, 'w+');
        if (!$sp8e6d86) {
            throw new \Exception('cant open file: '.$spd9c775);
        }
        $sp6e82aa = curl_init();
        curl_setopt($sp6e82aa, CURLOPT_URL, $spfaab7a);
        curl_setopt($sp6e82aa, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($sp6e82aa, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($sp6e82aa, CURLOPT_FILE, $sp8e6d86);
        if ($sp2b9683) {
            curl_setopt($sp6e82aa, CURLOPT_PROGRESSFUNCTION, function ($sp05c5a4, $sp5fe9ee, $spd54d78, $spcbc95b, $spd52407) {
                if ($sp5fe9ee > 0) {
                    echo '
    download: '.sprintf('%.2f', $spd54d78 / $sp5fe9ee * 100).'%';
                }
            });
            curl_setopt($sp6e82aa, CURLOPT_NOPROGRESS, false);
        }
        curl_setopt($sp6e82aa, CURLOPT_HEADER, 0);
        curl_setopt($sp6e82aa, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');
        if (defined('MY_PROXY')) {
            $sp87cc24 = MY_PROXY;
            $sp1e1501 = CURLPROXY_HTTP;
            if (strpos($sp87cc24, 'http://') || strpos($sp87cc24, 'https://')) {
                $sp87cc24 = str_replace('http://', $sp87cc24, $sp87cc24);
                $sp87cc24 = str_replace('https://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_HTTP;
            } elseif (strpos($sp87cc24, 'socks4://')) {
                $sp87cc24 = str_replace('socks4://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS4;
            } elseif (strpos($sp87cc24, 'socks4a://')) {
                $sp87cc24 = str_replace('socks4a://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS4A;
            } elseif (strpos($sp87cc24, 'socks5://')) {
                $sp87cc24 = str_replace('socks5://', $sp87cc24, $sp87cc24);
                $sp1e1501 = CURLPROXY_SOCKS5_HOSTNAME;
            }
            curl_setopt($sp6e82aa, CURLOPT_PROXY, $sp87cc24);
            curl_setopt($sp6e82aa, CURLOPT_PROXYTYPE, $sp1e1501);
            if (defined('MY_PROXY_PASS')) {
                curl_setopt($sp6e82aa, CURLOPT_PROXYUSERPWD, MY_PROXY_PASS);
            }
        }
        curl_exec($sp6e82aa);
        if (curl_errno($sp6e82aa)) {
            curl_close($sp6e82aa);
            throw new \Exception('curl_error: '.curl_error($sp6e82aa));
        }
        curl_close($sp6e82aa);

        return true;
    }

    public static function combineCookie($spdbdd55, $sp30e521)
    {
        $spa74fa8 = explode(';', $spdbdd55);
        $sp86fcbf = explode(';', $sp30e521);
        foreach ($spa74fa8 as $spf30099) {
            if (self::cookieIsExists($sp86fcbf, self::cookieGetName($spf30099)) == false) {
                array_push($sp86fcbf, $spf30099);
            }
        }
        $spe1b25a = '';
        foreach ($sp86fcbf as $spf30099) {
            if (substr($spf30099, -8, 8) != '=deleted' && strlen($spf30099) > 1) {
                $spe1b25a .= $spf30099.'; ';
            }
        }

        return substr($spe1b25a, 0, strlen($spe1b25a) - 1);
    }

    public static function cookieGetName($sp81e72b)
    {
        $spe192c7 = strpos($sp81e72b, '=');

        return substr($sp81e72b, 0, $spe192c7);
    }

    public static function cookieGetValue($sp81e72b)
    {
        $spe192c7 = strpos($sp81e72b, '=');
        $sp089f70 = substr($sp81e72b, $spe192c7 + 1, strlen($sp81e72b) - $spe192c7);

        return $sp089f70;
    }

    public static function cookieGet($spc94a6d, $spb54a76, $spbedaf7 = false)
    {
        $spc94a6d = str_replace(' ', '', $spc94a6d);
        if (substr($spc94a6d, -1, 1) != ';') {
            $spc94a6d = ';'.$spc94a6d.';';
        } else {
            $spc94a6d = ';'.$spc94a6d;
        }
        $sp56b3cd = Helper::str_between($spc94a6d, ';'.$spb54a76.'=', ';');
        if (!$spbedaf7 || $sp56b3cd == '') {
            return $sp56b3cd;
        } else {
            return $spb54a76.'='.$sp56b3cd;
        }
    }

    private static function cookieIsExists($spaa3e85, $sp462960)
    {
        foreach ($spaa3e85 as $spf30099) {
            if (self::cookieGetName($spf30099) == $sp462960) {
                return true;
            }
        }

        return false;
    }

    function test()
    {
        $sp089f70 = self::combineCookie('a=1;b=2;c=3', 'c=5');
        var_dump($sp089f70);
    }
}
<?php

namespace App\Library;

class UrlShorten
{
    public static function shorten($spb8b5f1, $spd7767f = false)
    {
        if ($spd7767f === false) {
            $spd7767f = \App\System::_get('domain_shorten');
        }
        if ($spd7767f === '1' || $spd7767f === 'url.cn') {
            $spa29f04 = UrlShorten::url_cn($spb8b5f1);
        } elseif ($spd7767f === '2' || $spd7767f === 't.cn') {
            $spa29f04 = UrlShorten::t_cn($spb8b5f1);
        } elseif ($spd7767f === 'w.url.cn') {
            $spa29f04 = UrlShorten::w_url_cn($spb8b5f1);
        } elseif ($spd7767f === 'custom') {
            $spa29f04 = UrlShorten::custom($spb8b5f1);
        } else {
            return $spb8b5f1;
        }
    }

    public static function t_cn_official($spb8b5f1)
    {
        $spb8b5f1 = urlencode($spb8b5f1);
        $sp7d75b6 = '2590114856';
        $sp9d9a09 = 'http://api.t.sina.com.cn/short_url/shorten.json?source='.$sp7d75b6.'&url_long='.$spb8b5f1;
        $sp950898 = curl_init();
        curl_setopt($sp950898, CURLOPT_URL, $sp9d9a09);
        curl_setopt($sp950898, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sp950898, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($sp950898, CURLOPT_HEADER, 0);
        curl_setopt($sp950898, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        $sp3e8d87 = curl_exec($sp950898);
        curl_close($sp950898);
        $sp6b8876 = json_decode($sp3e8d87, true);

        return isset($sp6b8876['url_short']) && strstr($sp6b8876['url_short'], 'http://') ? $sp6b8876['url_short']
            : null;
    }

    public static function t_cn($spb8b5f1)
    {
        $spb8b5f1 = urlencode($spb8b5f1);
        $sp9d9a09 = 'https://i.alapi.cn/url/?url='.$spb8b5f1;
        $sp950898 = curl_init();
        curl_setopt($sp950898, CURLOPT_URL, $sp9d9a09);
        curl_setopt($sp950898, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sp950898, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($sp950898, CURLOPT_HEADER, 0);
        curl_setopt($sp950898, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        $sp3e8d87 = curl_exec($sp950898);
        curl_close($sp950898);
        $sp6b8876 = json_decode($sp3e8d87, true);

        return isset($sp6b8876['shortUrl']) && strstr($sp6b8876['shortUrl'], 'http') ? $sp6b8876['shortUrl'] : null;
    }

    public static function url_cn($spb8b5f1)
    {
        $spb8b5f1 = urlencode($spb8b5f1);
        $sp9d9a09 = 'https://api.uomg.com/api/long2dwz?dwzapi=urlcn&url='.$spb8b5f1;
        $sp950898 = curl_init();
        curl_setopt($sp950898, CURLOPT_URL, $sp9d9a09);
        curl_setopt($sp950898, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sp950898, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($sp950898, CURLOPT_HEADER, 0);
        curl_setopt($sp950898, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($sp950898, CURLOPT_TIMEOUT, 5);
        curl_setopt($sp950898, CURLOPT_CONNECTTIMEOUT, 5);
        $sp3e8d87 = curl_exec($sp950898);
        curl_close($sp950898);
        $sp6b8876 = json_decode($sp3e8d87, true);

        return isset($sp6b8876['ae_url']) && strstr($sp6b8876['ae_url'], 'http') ? $sp6b8876['ae_url'] : null;
    }

    public static function w_url_cn($spb8b5f1)
    {
        return null;
    }

    public static function custom($spb8b5f1)
    {
        $sp77a861 = '';
        $spf04041 = '';
        $spb8b5f1 = urlencode($spb8b5f1);
        $sp9d9a09 = 'http://api.his.cat/api/url/shorten.json?id='.$sp77a861.'&key='.$spf04041.'&url='.$spb8b5f1;
        $sp950898 = curl_init();
        curl_setopt($sp950898, CURLOPT_URL, $sp9d9a09);
        curl_setopt($sp950898, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($sp950898, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($sp950898, CURLOPT_HEADER, 0);
        curl_setopt($sp950898, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($sp950898, CURLOPT_TIMEOUT, 5);
        curl_setopt($sp950898, CURLOPT_CONNECTTIMEOUT, 5);
        $sp3e8d87 = curl_exec($sp950898);
        curl_close($sp950898);
        $sp6b8876 = json_decode($sp3e8d87, true);

        return isset($sp6b8876['data']) && isset($sp6b8876['data']['short_url'])
               && strstr($sp6b8876['data']['short_url'], 'http') ? $sp6b8876['data']['short_url'] : null;
    }
}
<?php

namespace App\Library;

use Hashids\Hashids;

class Helper
{
    public static function getMysqlDate($spae8145 = 0)
    {
        return date('Y-m-d', time() + $spae8145 * 24 * 3600);
    }

    public static function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $spb53bff = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                    $spb53bff = $_SERVER['HTTP_CLIENT_IP'];
                } else {
                    $spb53bff = @$_SERVER['REMOTE_ADDR'];
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $spb53bff = getenv('HTTP_X_FORWARDED_FOR');
            } else {
                if (getenv('HTTP_CLIENT_IP')) {
                    $spb53bff = getenv('HTTP_CLIENT_IP');
                } else {
                    $spb53bff = getenv('REMOTE_ADDR');
                }
            }
        }
        if (strpos($spb53bff, ',') !== false) {
            $spa38edc = explode(',', $spb53bff);

            return $spa38edc[0];
        }

        return $spb53bff;
    }

    public static function getClientIP()
    {
        if (isset($_SERVER)) {
            $spb53bff = $_SERVER['REMOTE_ADDR'];
        } else {
            $spb53bff = getenv('REMOTE_ADDR');
        }
        if (strpos($spb53bff, ',') !== false) {
            $spa38edc = explode(',', $spb53bff);

            return $spa38edc[0];
        }

        return $spb53bff;
    }

    public static function filterWords($sp53772a, $sp773684)
    {
        if (!$sp53772a) {
            return false;
        }
        if (!is_array($sp773684)) {
            $sp773684 = explode('|', $sp773684);
        }
        foreach ($sp773684 as $sp11bc06) {
            if ($sp11bc06 && strpos($sp53772a, $sp11bc06) !== false) {
                return $sp11bc06;
            }
        }

        return false;
    }

    public static function is_idcard($spaa07e8)
    {
        if (strlen($spaa07e8) == 18) {
            return self::idcard_checksum18($spaa07e8);
        } elseif (strlen($spaa07e8) == 15) {
            $spaa07e8 = self::idcard_15to18($spaa07e8);

            return self::idcard_checksum18($spaa07e8);
        } else {
            return false;
        }
    }

    private static function idcard_verify_number($sp934b0f)
    {
        if (strlen($sp934b0f) != 17) {
            return false;
        }
        $sp7794af = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $sp1f7576 = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $sp74e276 = 0;
        for ($sp558f52 = 0; $sp558f52 < strlen($sp934b0f); $sp558f52++) {
            $sp74e276 += substr($sp934b0f, $sp558f52, 1) * $sp7794af[$sp558f52];
        }
        $sp1c5541 = $sp74e276 % 11;
        $sp59aaec = $sp1f7576[$sp1c5541];

        return $sp59aaec;
    }

    private static function idcard_15to18($sp7e7c7a)
    {
        if (strlen($sp7e7c7a) != 15) {
            return false;
        } else {
            if (array_search(substr($sp7e7c7a, 12, 3), ['996', '997', '998', '999']) !== false) {
                $sp7e7c7a = substr($sp7e7c7a, 0, 6).'18'.substr($sp7e7c7a, 6, 9);
            } else {
                $sp7e7c7a = substr($sp7e7c7a, 0, 6).'19'.substr($sp7e7c7a, 6, 9);
            }
        }
        $sp7e7c7a = $sp7e7c7a.self::idcard_verify_number($sp7e7c7a);

        return $sp7e7c7a;
    }

    private static function idcard_checksum18($sp7e7c7a)
    {
        if (strlen($sp7e7c7a) != 18) {
            return false;
        }
        $sp934b0f = substr($sp7e7c7a, 0, 17);
        if (self::idcard_verify_number($sp934b0f) != strtoupper(substr($sp7e7c7a, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }

    public static function str_between($sp53772a, $spb070e6, $sp3ddd95)
    {
        $spf18dda = strpos($sp53772a, $spb070e6);
        if ($spf18dda === false) {
            return '';
        }
        $spd283bf = strpos($sp53772a, $sp3ddd95, $spf18dda + strlen($spb070e6));
        if ($spd283bf === false || $spf18dda >= $spd283bf) {
            return '';
        }
        $sp9026fa = strlen($spb070e6);
        $spa59707 = substr($sp53772a, $spf18dda + $sp9026fa, $spd283bf - $spf18dda - $sp9026fa);

        return $spa59707;
    }

    public static function str_between_longest($sp53772a, $spb070e6, $sp3ddd95)
    {
        $spf18dda = strpos($sp53772a, $spb070e6);
        if ($spf18dda === false) {
            return '';
        }
        $spd283bf = strrpos($sp53772a, $sp3ddd95, $spf18dda + strlen($spb070e6));
        if ($spd283bf === false || $spf18dda >= $spd283bf) {
            return '';
        }
        $sp9026fa = strlen($spb070e6);
        $spa59707 = substr($sp53772a, $spf18dda + $sp9026fa, $spd283bf - $spf18dda - $sp9026fa);

        return $spa59707;
    }

    public static function format_url($spfaab7a)
    {
        if (!strlen($spfaab7a)) {
            return $spfaab7a;
        }
        if (!starts_with($spfaab7a, 'http://') && !starts_with($spfaab7a, 'https://')) {
            $spfaab7a = 'http://'.$spfaab7a;
        }
        while (ends_with($spfaab7a, '/')) {
            $spfaab7a = substr($spfaab7a, 0, -1);
        }

        return $spfaab7a;
    }

    public static function lite_hash($sp53772a)
    {
        $spade028 = crc32((string)$sp53772a);
        if ($spade028 < 0) {
            $spade028 &= 1 << 7;
        }

        return $spade028;
    }

    const ID_TYPE_USER      = 0;
    const ID_TYPE_CATEGORY  = 1;
    const ID_TYPE_PRODUCT   = 2;
    const ID_TYPE_AFFILIATE = 3;

    public static function id_encode($spdc31ea, $spa8b0dd, ...$spfb4409)
    {
        $sp0da0c6 = new Hashids(config('app.key'), 8, 'abcdefghijklmnopqrstuvwxyz1234567890');

        return @$sp0da0c6->encode(self::lite_hash($spdc31ea), $spdc31ea, self::lite_hash($spa8b0dd), $spa8b0dd, ...$spfb4409);
    }

    public static function id_decode($sp0907f2, $spa8b0dd, &$sp32e3c9 = false)
    {
        if (strlen($sp0907f2) < 8) {
            $sp0da0c6 = new Hashids(config('app.key'));
            if ($spa8b0dd === self::ID_TYPE_USER) {
                return intval(@$sp0da0c6->decodeHex($sp0907f2));
            } else {
                return intval(@$sp0da0c6->decode($sp0907f2)[0]);
            }
        }
        $sp0da0c6 = new Hashids(config('app.key'), 8, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $sp32e3c9 = @$sp0da0c6->decode($sp0907f2) ?? [];

        return intval($sp32e3c9[1]);
    }

    public static function is_mobile()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            if (preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
        }

        return false;
    }

    public static function b1_rand_background()
    {
        if (self::is_mobile()) {
            $spdf13e5 = [
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpgyq8n5j20u01hcne2.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpfyjbd0j20u01hcte2.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpw3b5mkj20u01hcnfh.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoybkicbj20u01hc7de.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpes8rmmj20u01hctn7.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp8ond6gj20u01hctji.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp4ljhhvj20u01hck0r.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpstrwnsj20u01hc7he.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxq2a1vthj20u01hc4gs.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpiebjztj20u01hcaom.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxow4b14kj20u01hc43x.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxohtyvgfj20u01hc7gk.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxp6vexa3j20u01hcdj3.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxqa0zhc6j20u01hc14e.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxomnbr0gj20u01hc79r.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpx57f0sj20u01hcqmd.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoozjilyj20u01hcgt9.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxprigfw1j20u01hcam9.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxod70fcpj20u01hcajj.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpzb5p1tj20u01hcnca.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxozvry57j20u01hcgwo.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpv092lfj20u01hcx1o.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpdz6s0bj20u01hcaqj.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxoso79ayj20u01hcq9c.jpg',
                '//ww2.sinaimg.cn/large/ac1a0c4agy1ftxpqjrtjhj20u01hcapi.jpg',
            ];
        } else {
            $spdf13e5 = [
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz78cfrj2j21hc0u0kio.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7qj6l3xj21hc0u0b29.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9tqa2fvpj21hc0u017a.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz71m76skj21hc0u0nnq.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz709py6fj21hc0u0wx2.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgqv33lj21hc0u04qp.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9s9soh4sj21hc0u01kx.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9s9r2vkzj21hc0u0x4e.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7etbcs8j21hc0u07p3.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgn1bluj21hc0u0kiy.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7r6tmv1j21hc0u0anj.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7c4h0xzj21hc0u01kx.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9tq7uypvj21hc0u01be.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4pjgbncj21hc0u0kjl.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7i6u1gxj21hc0u0tyk.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4s0fb2tj21hc0u01ky.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz72wkr9dj21hc0u0h1r.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7tj5ohrj21hc0u0qnp.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ft9sgp23zbj21hc0u0txl.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7l9dcokj21hc0u0k9k.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1fwr4lvumu1j21hc0u0x6p.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz7alxyhnj21hc0u0nkh.jpg',
                '//ww1.sinaimg.cn/large/ac1a0c4agy1ftz799gvb3j21hc0u0qdt.jpg',
            ];
        }

        return $spdf13e5[rand(0, count($spdf13e5) - 1)];
    }

    public static function isWakePassword($sp02bf0b)
    {
        $sp7a9e07 = [
            '123456',
            'password',
            '12345678',
            'qwerty',
            '123456789',
            '12345',
            '1234',
            '111111',
            '1234567',
            'dragon',
            '123123',
            'baseball',
            'abc123',
            'football',
            'monkey',
            'letmein',
            '696969',
            'shadow',
            'master',
            '666666',
            'qwertyuiop',
            '123321',
            'mustang',
            '1234567890',
            'michael',
            '654321',
            'pussy',
            'superman',
            '1qaz2wsx',
            '7777777',
            'fuckyou',
            '121212',
            '000000',
            'qazwsx',
            '123qwe',
            'killer',
            'trustno1',
            'jordan',
            'jennifer',
            'zxcvbnm',
            'asdfgh',
            'hunter',
            'buster',
            'soccer',
            'harley',
            'batman',
            'andrew',
            'tigger',
            'sunshine',
            'iloveyou',
            'fuckme',
            '2000',
            'charlie',
            'robert',
            'thomas',
            'hockey',
            'ranger',
            'daniel',
            'starwars',
            'klaster',
            '112233',
            'george',
            'asshole',
            'computer',
            'michelle',
            'jessica',
            'pepper',
            '1111',
            'zxcvbn',
            '555555',
            '11111111',
            '131313',
            'freedom',
            '777777',
            'pass',
            'fuck',
            'maggie',
            '159753',
            'aaaaaa',
            'ginger',
            'princess',
            'joshua',
            'cheese',
            'amanda',
            'summer',
            'love',
            'ashley',
            '6969',
            'nicole',
            'chelsea',
            'biteme',
            'matthew',
            'access',
            'yankees',
            '987654321',
            'dallas',
            'austin',
            'thunder',
            'taylor',
            'matrix',
            'minecraft',
        ];

        return in_array($sp02bf0b, $sp7a9e07);
    }
}
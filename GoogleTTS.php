<?php
namespace Yuunie;

class GoogleTTS
{
	/**
	 * 获得tts链接中tk值所对应的tkk值
	 * @return string tkk值
	 */
    public static function getTkk()
    {
        $url = "https://translate.google.cn/";
        $html = file_get_contents($url);

        $x = "#TKK\=eval\('\(\(function\(\)\{var\s+a\\\\x3d(-?\d+);var\s+b\\\\x3d(-?\d+);return\s+(\d+)\+#isU";

        if (preg_match($x, $html, $arr))
        {
            $tkk = $arr[3] .'.'.($arr[1] + $arr[2]) ;
            return $tkk ;

        } else {
            return '426465.1673265149';
        }
    }

    /**
     * 获得tts链接中所需要的tk值
     * @param  string $a tss文本
     * @param  string $b tkk值
     * @return string    tk
     */
    public static function getTk($a = '', $b = '426465.1673265149')
    {
        // 源码来自https://www.52pojie.cn/forum.php?mod=viewthread&tid=707169
        $d = explode('.', $b);
        $b = intval($d[0]);

        for ($e = array(), $f = 0, $g = 0; $g < mb_strlen($a); $g++) {
            $l = self::charCodeAt($a, $g);
            if (128 > $l) {
                $e[$f++] = $l;
            } else {
                if (2048 > $l) {
                    $e[$f++] = $l >> 6 | 192;
                } else {
                    if (55296 == ($l & 64512) && $g + 1 < strlen($a) && 56320 == (self::charCodeAt($a, $g + 1) & 64512)) {
                        $l = 65536 + (($l & 1023) << 10) + (self::charCodeAt($a, ++$g) & 1023);
                        $e[$f++] = $l >> 18 | 240;
                        $e[$f++] = $l >> 12 & 63 | 128;
                    } else {
                        $e[$f++] = $l >> 12 | 224;
                        $e[$f++] = $l >> 6 & 63 | 128;
                        $e[$f++] = $l & 63 | 128;
                    }
                }
            }
        }

        $a = $b;

        for ($f = 0; $f < count($e); $f++) {
            $a += $e[$f];
            $a = self::tq($a, "+-a^+6");
        }

        $a = self::tq($a, "+-3^+b+-f");
        $a ^= floatval($d[1]);
        0 > $a && ($a = ($a & 2147483647) + 2147483648);

        $a = intval(fmod(floatval($a), 1000000));
        
        return (string)(strval($a) . "." . strval(($a ^ $b)));
    }

    /**
     * 获得tss链接
     * @param  string $text     文本
     * @param  string $ttsspeed tts播放速度0-1
     * @param  string $tl       tts播放语言
     * @return string           url
     * PS: 通过链接下载mp3使用,直接使用链接可能会出现一些奇怪的问题
     */
    public static function getUrl($text='',$ttsspeed = '2', $tl = 'zh-CN')
    {
        $textlen = mb_strlen($text);
        $ttsspeed = intval($ttsspeed);
        if ($ttsspeed == '2') {
            $ttsspeed = rand(0, 1);
        }
        $tk = self::getTk($text, self::getTkk());
        $url = "https://translate.google.cn/translate_tts?ie=UTF-8&q={$text}&tl={$tl}&total=1&idx=0&textlen={$textlen}&tk={$tk}&client=t&ttsspeed={$ttsspeed}";

        return $url;
    }

    private static function charCodeAt($str, $index)
    {
        $char = mb_substr($str, $index, 1, 'UTF-8');

        if (mb_check_encoding($char, 'UTF-8')) {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
            return hexdec(bin2hex($ret));
        } else {
            return null;
        }
    }

    private static function tq($a, $b)
    {
        for ($c = 0; $c < strlen($b) - 2; $c += 3) {
            $d = $b[$c + 2];
            $d = "a" <= $d ? self::charCodeAt($d, 0) - 87 : intval($d);
            $d = "+" == $b[$c + 1] ? self::demo($a, $d) : $a << $d;
            $a = "+" == $b[$c] ? $a + $d & 4294967295 : $a ^ $d;
        }
        return $a;
    }

    private static function demo($v, $y)
    {
        $t = $v >> $y;
        $f = 0x7fffffff >> ($y - 1);
        return ($t & $f);
    }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('getGBK')) {
    function getGBK($key)
    {

        if (strtoupper(CHARSET) == 'GBK' && !empty($key)) {
            if (is_array($key)) {
                $result = var_export($key, true);//变为字符串
                $result = iconv('UTF-8', 'GBK', $result);
                eval("\$result = $result;");//转换回数组
            } else {
                $result = iconv('UTF-8', 'GBK', $key);
            }
        }
        return $result;
    }
}

if ( ! function_exists('getUTF8')) {
    function getUTF8($key)
    {
        /**
         * 转码
         */
        if (!empty($key)) {
            if (is_array($key)) {
                $result = var_export($key, true);//变为字符串
                $result = iconv('GBK', 'UTF-8', $result);
                eval("\$result = $result;");//转换回数组
            } else {
                $result = iconv('GBK', 'UTF-8', $key);
            }
        }
        return $result;
    }
}

if ( ! function_exists('unicodeToUtf8')) {
    function unicodeToUtf8($str, $order = "little")
    {
        $utf8string = "";
        $n = strlen($str);
        for ($i = 0; $i < $n; $i++) {
            if ($order == "little") {
                $val = str_pad(dechex(ord($str[$i + 1])), 2, 0, STR_PAD_LEFT) .
                    str_pad(dechex(ord($str[$i])), 2, 0, STR_PAD_LEFT);
            } else {
                $val = str_pad(dechex(ord($str[$i])), 2, 0, STR_PAD_LEFT) .
                    str_pad(dechex(ord($str[$i + 1])), 2, 0, STR_PAD_LEFT);
            }
            $val = intval($val, 16); // 由于上次的.连接，导致$val变为字符串，这里得转回来。
            $i++; // 两个字节表示一个unicode字符。
            $c = "";
            if ($val < 0x7F) { // 0000-007F
                $c .= chr($val);
            } elseif ($val < 0x800) { // 0080-07F0
                $c .= chr(0xC0 | ($val / 64));
                $c .= chr(0x80 | ($val % 64));
            } else { // 0800-FFFF
                $c .= chr(0xE0 | (($val / 64) / 64));
                $c .= chr(0x80 | (($val / 64) % 64));
                $c .= chr(0x80 | ($val % 64));
            }
            $utf8string .= $c;
        }
        /* 去除bom标记 才能使内置的iconv函数正确转换 */
        if (ord(substr($utf8string, 0, 1)) == 0xEF && ord(substr($utf8string, 1, 2)) == 0xBB && ord(substr($utf8string, 2, 1)) == 0xBF) {
            $utf8string = substr($utf8string, 3);
        }
        return $utf8string;
    }
}
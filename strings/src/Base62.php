<?php

namespace Ast\UtilString;

class Base62
{
    private static $INDEX = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    static public function codeNumber($number)
    {
        $index = self::$INDEX;//"abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $base  = strlen($index);

        $out = "";
        for ($t = floor(log($number, $base)); $t >= 0; $t--) {
            $bcp = bcpow($base, $t);
            $a   = floor($number / $bcp) % $base;
            $out = $out . substr($index, $a, 1);
            $number  = $number - ($a * $bcp);
        }
        return  strrev($out); // reverse
    }

    static public function decodeNumber($number)
    {
        $index = self::$INDEX;//"abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $base  = strlen($index);

        $in  = strrev($number);
        $out = 0;
        $len = strlen($in) - 1;
        for ($t = 0; $t <= $len; $t++) {
            $bcpow = bcpow($base, $len - $t);
            $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
        }

        $out = sprintf('%F', $out);
        return substr($out, 0, strpos($out, '.'));
    }
}
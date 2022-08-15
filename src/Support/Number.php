<?php

namespace O21\CryptoWallets\Support;

class Number
{
    public static function trimRightZero($number): string
    {
        $number = (string) $number;
        $splits = explode('.', $number);
        if (count($splits) > 1) {
            $splits[1] = rtrim($splits[1], '0');
        }
        $number = implode('.', $splits);
        return rtrim($number, '.');
    }
}
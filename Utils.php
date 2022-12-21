<?php

namespace O21\CryptoWallets;

class Utils
{
    public static function jsonDecode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException('json_decode error: ' . \json_last_error_msg());
        }

        return $data;
    }
}
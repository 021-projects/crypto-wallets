<?php

namespace O21\CryptoWallets\Support;

class Error
{
    public static function assertEmpty($err): void
    {
        throw_if($err instanceof \Exception, $err);
        throw_if(is_string($err), \RuntimeException::class, $err);
    }
}
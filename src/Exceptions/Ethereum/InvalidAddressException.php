<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class InvalidAddressException extends Exception
{
    public function __construct(
        string $address,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            "Invalid address ($address). Please pass a valid address to a function.",
            $code,
            $previous
        );
    }
}
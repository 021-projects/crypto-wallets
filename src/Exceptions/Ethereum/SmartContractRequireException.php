<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class SmartContractRequireException extends Exception
{
    public function __construct(
        string $message = "This function require smart contract.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
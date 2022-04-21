<?php

namespace O21\CryptoWallets\Exceptions\Bitcoind;

use Exception;

class FundRawTransactionException extends Exception
{
    public function __construct(
        string $message = "Something went wrong when fund a raw transaction.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace O21\CryptoWallets\Exceptions;

use Exception;

class RateProviderDoesntSupportsHistory extends Exception
{
    public function __construct(
        string $message = "Rate provider does not support rate history.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
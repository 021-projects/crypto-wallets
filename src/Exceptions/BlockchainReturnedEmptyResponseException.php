<?php

namespace O21\CryptoWallets\Exceptions;

use Exception;

class BlockchainReturnedEmptyResponseException extends Exception
{
    public function __construct(
        string $message = "Blockhain returned empty response.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
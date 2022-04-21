<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class SmartContractNotDeployedException extends Exception
{
    public function __construct(
        string $message = "Smart contract not deployed.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class CantGetNetworkStatsException extends Exception
{
    public function __construct(
        string $message = "Can't get network statistics for Ethereum blockchain. All oracles are unavailable.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
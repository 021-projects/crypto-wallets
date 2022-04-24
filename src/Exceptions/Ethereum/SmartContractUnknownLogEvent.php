<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class SmartContractUnknownLogEvent extends Exception
{
    public function __construct(string $topic)
    {
        parent::__construct("Unknown log event with topic: $topic.");
    }
}
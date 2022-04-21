<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class SmartContractAlreadyDeployedException extends Exception
{
    public function __construct(
        string $address,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            "Smart contract ($address) already deployed. Create a new one contract class.",
            $code,
            $previous
        );
    }
}
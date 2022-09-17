<?php

namespace O21\CryptoWallets\Support;

use O21\CryptoWallets\Interfaces\FeeInterface;

class Fee
{
    public static function getValue(FeeInterface|string $fee): string
    {
        return $fee instanceof FeeInterface
            ? $fee->getValue()
            : $fee;
    }
}

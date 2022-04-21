<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Concerns\GetRatesTrait;
use O21\CryptoWallets\Interfaces\FeeInterface;
use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;

abstract class AbstractWallet
{
    use GetRatesTrait;

    abstract protected function getRateProvider(?RateProvider $provider = null): RateProvider;

    protected function feeValue(FeeInterface|string $fee): string
    {
        return $fee instanceof FeeInterface
            ? $fee->getValue()
            : $fee;
    }
}
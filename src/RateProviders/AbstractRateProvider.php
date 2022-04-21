<?php

namespace O21\CryptoWallets\RateProviders;

use O21\CryptoWallets\Exceptions\RateProviderDoesntSupportsHistory;
use O21\CryptoWallets\Interfaces\RateProviderInterface;
use O21\CryptoWallets\Units\RateInterval;

abstract class AbstractRateProvider implements RateProviderInterface
{
    public function isSupportsHistory(): bool
    {
        return false;
    }

    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 60,
        RateInterval $interval = RateInterval::Minutes
    ): array {
        throw new RateProviderDoesntSupportsHistory;
    }

    protected function floatMap(array $array, string $key = ''): array
    {
        return array_map(
            static fn($value) => (float)($key ? $value[$key] : $value),
            $array
        );
    }
}

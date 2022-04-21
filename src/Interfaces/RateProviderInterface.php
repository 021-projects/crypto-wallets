<?php

namespace O21\CryptoWallets\Interfaces;

use O21\CryptoWallets\Units\RateInterval;

interface RateProviderInterface
{
    /**
     * Checks history support.
     *
     * @return bool
     */
    public function isSupportsHistory(): bool;

    /**
     * Returns exchange rate.
     *
     * @param  string  $toSymbol
     * @param  string  $fromSymbol
     * @return float
     */
    public function getRate(string $toSymbol, string $fromSymbol = ''): float;

    /**
     * Returns history of exchange rates.
     *
     * @param  string  $toSymbol
     * @param  string  $fromSymbol
     * @param  int  $limit
     * @param  \O21\CryptoWallets\Units\RateInterval  $interval
     * @return array
     */
    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 60,
        RateInterval $interval = RateInterval::Minutes
    ): array;
}

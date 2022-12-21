<?php

namespace O21\CryptoWallets\Contracts;

interface WalletRate
{
    public const INTERVAL_MINUTES = 0x01;
    public const INTERVAL_HOURS   = 0x02;
    public const INTERVAL_DAYS    = 0x03;

    public const RATE_BINANCE = 'binance';
    public const RATE_BLOCKCHAIN = 'blockchain';
    public const RATE_COINGECKO = 'coingecko';

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
     * @param  int  $interval
     * @return array|float[]
     */
    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 100,
        int $interval = self::INTERVAL_HOURS
    ): array;
}

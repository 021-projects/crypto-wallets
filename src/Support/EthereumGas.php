<?php

namespace O21\CryptoWallets\Support;

use O21\CryptoWallets\Interfaces\FeeInterface;

class EthereumGas
{
    /**
     * Calculate fee for transaction send based on used gas
     *
     * @param  string|int  $gas
     * @param  string|int  $baseFee
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string  $fee
     * @return string in Wei
     */
    public static function estimateMaxGasFee(
        string|int $gas,
        string|int $baseFee,
        FeeInterface|string $fee
    ): string {
        return bcmul(
            (string)$gas,
            bcadd(Fee::getValue($fee), bcmul('2', (string)$baseFee))
        );
    }
}

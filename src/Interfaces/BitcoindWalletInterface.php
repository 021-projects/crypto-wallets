<?php

namespace O21\CryptoWallets\Interfaces;

interface BitcoindWalletInterface
{
    /**
     * @param  string  $to
     * @param  string  $amount
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string $fee value per kbyte
     * @return array
     * @throws \Throwable
     */
    public function createAndFundTransaction(
        string $to,
        string $amount,
        FeeInterface|string $fee
    ): array;

    public function createRawTransaction(
        array $inputs = [],
        array $outputs = [],
        int $lockTime = 0,
        bool $replaceable = false
    ): string;

    public function fundRawTransaction(string $hexString, ?array $options = null): array;
}
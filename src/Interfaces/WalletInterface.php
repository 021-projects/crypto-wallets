<?php

namespace O21\CryptoWallets\Interfaces;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Units\RateInterval;

interface WalletInterface
{
    public function __construct(ConnectConfigInterface $config);

    public function isAvailable(): bool;

    public function getBalance(): string;

    public function getNewAddress($config = null): string;

    public function isValidAddress(string $address): bool;

    public function isOwningAddress(string $address): bool;

    public function getExploreAddressLink(string $address): string;

    /**
     * Returns exchange rate.
     *
     * @param  string  $currency
     * @param  \O21\CryptoWallets\Interfaces\RateProviderInterface|null  $provider
     * @return float
     */
    public function getRate(
        string $currency = 'USD',
        ?RateProviderInterface $provider = null
    ): float;

    /**
     * Returns best exchange rate based on history.
     *
     * @param  string  $currency
     * @param  int  $limit
     * @param  \O21\CryptoWallets\Units\RateInterval  $interval
     * @param  \O21\CryptoWallets\Interfaces\RateProviderInterface|null  $provider
     * @return float
     */
    public function getBestRate(
        string $currency = 'USD',
        int $limit = 60,
        RateInterval $interval = RateInterval::Minutes,
        ?RateProviderInterface $provider = null
    ): float;

    /**
     * @param  string  $to
     * @param  string  $value
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string  $fee
     * @return string Fee amounts for transaction.
     */
    public function estimateSendingFee(
        string $to,
        string $value,
        FeeInterface|string $fee
    ): string;

    /**
     * @param  string  $to
     * @param  string  $value
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string  $fee
     * @return string  ID of new transaction
     */
    public function send(string $to, string $value, FeeInterface|string $fee): string;

    public function getTransaction(string $hash): ?TransactionInterface;

    public function getTransactions(int $count = 50, int $skip = 0): Collection;

    /**
     * @param  string  $block
     * @return \Illuminate\Support\Collection<\O21\CryptoWallets\Interfaces\TransactionInterface>
     */
    public function getTransactionsSinceBlock(string $block): Collection;

    public function getTransactionsCount(): int;

    public function getExploreTransactionLink(string $hash): string;

    /**
     * @return \Illuminate\Support\Collection<\O21\CryptoWallets\Interfaces\FeeInterface>
     */
    public function getNetworkFees(): Collection;

    public function getDefaultBestRateLimit(): int;

    public function getTypicalTransactionSize(): int;

    public function getSymbol(): string;
}

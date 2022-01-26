<?php

namespace O21\CryptoWallets\Contracts;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Transaction;

interface CryptoWallet
{
    public function __construct(array $config, string $walletName = '');

    public function getBalance(): float;

    public function getTransactionsCount(): int;

    /**
     * Returns exchange rate.
     *
     * @param  string|null  $currency
     * @param  string|null  $source
     * @return float
     */
    public function getRate(string $currency = 'USD', ?string $source = null): float;

    /**
     * Returns best exchange rate based on history.
     *
     * @param  string|null  $currency
     * @param  int  $limit
     * @param  int  $interval
     * @return float
     */
    public function getBestRate(
        string $currency = 'USD',
        int $limit = 100,
        int $interval = WalletRate::INTERVAL_MINUTES
    ): float;

    public function isAvailable(): bool;

    public function getNewAddress(): string;

    public function validateAddress(string $address): bool;

    public function getExploreAddressLink(string $address): string;

    /**
     * @param array|string $addresses
     * @param  float  $amount
     * @param  float  $fee
     * @param  string|null  $error
     * @return float
     */
    public function calcAmountIncludingFee($addresses, float $amount, float $fee, ?string &$error = null): float;

    /**
     * @param  array|string  $addresses
     * @param  float  $amount
     * @param  float  $fee
     * @param  string|null  $error
     * @return string|bool ID of new transaction
     */
    public function sendToAddress($addresses, float $amount, float $fee, ?string &$error = null);

    public function getTransaction(string $txid): Transaction;

    public function getTransactions(int $count = 50, int $skip = 0): Collection;

    public function getExploreTransactionLink(string $txid): string;

    /**
     * @return \Illuminate\Support\Collection|\O21\CryptoWallets\Estimates\Fee[]
     */
    public function getEstimateFees(): Collection;

    /**
     * @return \Illuminate\Support\Collection|\O21\CryptoWallets\Transaction[]
     */
    public function getTransactionsSinceBlock(string $block = ''): Collection;

    public function getDefaultBestRateLimit(): int;

    public function getTypicalTransactionSize(): int;

    public function getSymbol(): string;
}

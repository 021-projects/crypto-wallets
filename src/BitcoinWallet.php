<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;
use Illuminate\Support\Collection;
use O21\CryptoWallets\Fees\BitcoindFee;
use O21\CryptoWallets\RateProviders\BinanceProvider;

class BitcoinWallet extends AbstractBitcoindWallet
{
    public function getExploreAddressLink(string $address): string
    {
        return sprintf('https://blockchair.com/bitcoin/address/%s', $address);
    }

    public function getDefaultBestRateLimit(): int
    {
        return 60;
    }

    protected function getDefaultFees(): Collection
    {
        return collect([
            new BitcoindFee(0.00003012, 2 * 60 * 10, 2),
            new BitcoindFee(0.00002235, 4 * 60 * 10, 4),
            new BitcoindFee(0.00002233, 6 * 60 * 10, 6),
            new BitcoindFee(0.00001082, 12 * 60 * 10, 12),
            new BitcoindFee(0.00001000, 24 * 60 * 10, 24)
        ]);
    }

    public function getExploreTransactionLink(string $hash): string
    {
        return sprintf('https://blockchair.com/bitcoin/transaction/%s', $hash);
    }

    protected function getRateProvider(?RateProvider $provider = null): RateProvider
    {
        return $provider ?? new BinanceProvider;
    }

    public function getTypicalTransactionSize(): int
    {
        return 350;
    }

    public function getSymbol(): string
    {
        return 'BTC';
    }
}
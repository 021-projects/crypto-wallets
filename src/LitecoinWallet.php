<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;
use O21\CryptoWallets\Fees\BitcoindFee;
use Illuminate\Support\Collection;
use O21\CryptoWallets\RateProviders\BinanceProvider;

class LitecoinWallet extends AbstractBitcoindWallet
{
    protected array $confirmationBlocks = [
        2
    ];

    public function getExploreAddressLink(string $address): string
    {
        return sprintf('https://blockchair.com/litecoin/address/%s', $address);
    }

    public function getDefaultBestRateLimit(): int
    {
        return 15;
    }

    protected function getDefaultFees(): Collection
    {
        return collect([
            new BitcoindFee(0.00001000, 2)
        ]);
    }

    public function getExploreTransactionLink(string $hash): string
    {
        return sprintf('https://blockchair.com/litecoin/transaction/%s', $hash);
    }

    protected function getRateProvider(?RateProvider $provider = null): RateProvider
    {
        return $provider ?? new BinanceProvider;
    }

    public function getTypicalTransactionSize(): int
    {
        return 440;
    }

    public function getSymbol(): string
    {
        return 'LTC';
    }
}

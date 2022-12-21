<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Contracts\WalletRate;
use O21\CryptoWallets\Estimates\Fee;
use Illuminate\Support\Collection;
use O21\CryptoWallets\Rates\Binance;
use O21\CryptoWallets\Rates\Bitcoin\Blockchain;
use O21\CryptoWallets\Rates\CoinGeckoProvider;

class LitecoinWallet extends BitcoindWallet
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
            new Fee(0.00001000, 2)
        ]);
    }

    public function getExploreTransactionLink(string $txid): string
    {
        return sprintf('https://blockchair.com/litecoin/transaction/%s', $txid);
    }

    protected function getWalletRate(?string $source = null): WalletRate
    {
        switch ($source) {
            default:
            case WalletRate::RATE_BINANCE:
                return new Binance;

            case WalletRate::RATE_COINGECKO:
                return new CoinGeckoProvider;
        }
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

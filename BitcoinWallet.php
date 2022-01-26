<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Contracts\WalletRate;
use Illuminate\Support\Collection;
use O21\CryptoWallets\Estimates\Fee;
use O21\CryptoWallets\Rates\Binance;
use O21\CryptoWallets\Rates\Bitcoin\Blockchain;

class BitcoinWallet extends BitcoindWallet
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
            new Fee(0.00003012, 2),
            new Fee(0.00002235, 4),
            new Fee(0.00002233, 6),
            new Fee(0.00001082, 12),
            new Fee(0.00001000, 24)
        ]);
    }

    public function getExploreTransactionLink(string $txid): string
    {
        return sprintf('https://blockchair.com/bitcoin/transaction/%s', $txid);
    }

    protected function getWalletRate(?string $source = null): WalletRate
    {
        switch ($source) {
            default:
            case WalletRate::RATE_BINANCE:
                return new Binance;

            case WalletRate::RATE_BLOCKCHAIN:
                return new Blockchain;
        }
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
<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Interfaces\ERC20\TokenContractInterface as ITokenContract;
use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;
use O21\CryptoWallets\SmartContracts\Ethereum\TetherSmartContract;
use O21\CryptoWallets\Units\RateInterval;

class TetherWallet extends AbstractERC20Wallet
{
    public function getRate(
        string $currency = 'USD',
        ?RateProvider $provider = null
    ): float {
        if ($currency === 'USD') {
            return 1;
        }

        return parent::getRate($currency, $provider);
    }

    public function getBestRate(
        string $currency = 'USD',
        int $limit = null,
        RateInterval $interval = RateInterval::Minutes,
        ?RateProvider $provider = null
    ): float {
        if ($currency === 'USD') {
            return 1;
        }

        return parent::getBestRate($currency, $limit, $interval, $provider);
    }

    protected function _createSmartContract(?string $address = null): ITokenContract
    {
        return new TetherSmartContract($this, $address);
    }

    public function getSymbol(): string
    {
        return 'USDT';
    }
}
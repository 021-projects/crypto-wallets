<?php

namespace Tests\Concerns;

use O21\CryptoWallets\Configs\ConnectConfig;
use O21\CryptoWallets\EthereumWallet;

trait EthereumWalletTrait
{
    protected EthereumWallet $wallet;

    protected function setUpEthereumClient(): void
    {
        $this->wallet = new EthereumWallet(
            new ConnectConfig(
                $_ENV['ETH_USER'],
                $_ENV['ETH_PASSWORD'],
                $_ENV['ETH_HOST'],
                $_ENV['ETH_PORT'],
            )
        );
    }
}
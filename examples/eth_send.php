<?php

use O21\CryptoWallets\Configs\ConnectConfig;
use O21\CryptoWallets\EthereumWallet;
use O21\CryptoWallets\Units\Ethereum;

$wallet = new EthereumWallet(
    new ConnectConfig(
        $_ENV['ETH_USER'],
        $_ENV['ETH_PASSWORD'],
        $_ENV['ETH_HOST'],
        $_ENV['ETH_PORT'],
    )
);

$hash = $wallet->send(
    to: '0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea',
    value: Ethereum::Ether->toWei(0.001),
    fee: $wallet->getNetworkFees()->first(),
    from: $wallet->getCoinbase()
);

$tx = $wallet->getTransaction($hash);
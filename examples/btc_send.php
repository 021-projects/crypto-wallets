<?php

use O21\CryptoWallets\BitcoinWallet;
use O21\CryptoWallets\Configs\ConnectConfig;

$wallet = new BitcoinWallet(
    new ConnectConfig(
        $_ENV['BITCOIN_USER'],
        $_ENV['BITCOIN_PASSWORD'],
        $_ENV['BITCOIN_HOST'],
        $_ENV['BITCOIN_PORT'],
    )
);

$hash = $wallet->send(
    to: 'bcrt1q0t7kyuarqnhrpvnpz0jzx6lefwy80vlak36zya',
    value: '0.001',
    fee: $wallet->getNetworkFees()->first()
);

$tx = $wallet->getTransaction($hash);
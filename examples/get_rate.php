<?php

use O21\CryptoWallets\Configs\ConnectConfig;
use O21\CryptoWallets\BitcoinWallet;
use O21\CryptoWallets\EthereumWallet;
use O21\CryptoWallets\Units\RateInterval;

$bitcoin = new BitcoinWallet(
    new ConnectConfig(
        $_ENV['BITCOIN_USER'],
        $_ENV['BITCOIN_PASSWORD'],
        $_ENV['BITCOIN_HOST'],
        $_ENV['BITCOIN_PORT'],
    )
);

$ethereum = new EthereumWallet(
    new ConnectConfig(
        $_ENV['ETH_USER'],
        $_ENV['ETH_PASSWORD'],
        $_ENV['ETH_HOST'],
        $_ENV['ETH_PORT'],
    )
);

$bitcoinUsdRate = $bitcoin->getRate();
$ethereumUsdRate = $ethereum->getRate();

$bestBitcoinRateForLastHour = $bitcoin->getBestRate(limit: 1, interval: RateInterval::Hours);
$bestEthereumRateForLastHour = $ethereum->getBestRate(limit: 1, interval: RateInterval::Hours);
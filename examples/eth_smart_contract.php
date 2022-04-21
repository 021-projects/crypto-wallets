<?php

use O21\CryptoWallets\Configs\ConnectConfig;
use O21\CryptoWallets\EthereumWallet;
use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;
use O21\CryptoWallets\Units\Ethereum;
use Tests\SmartContracts\Ethereum\TestContract;

$wallet = new EthereumWallet(
    new ConnectConfig(
        $_ENV['ETH_USER'],
        $_ENV['ETH_PASSWORD'],
        $_ENV['ETH_HOST'],
        $_ENV['ETH_PORT'],
    )
);

$contract = $wallet->getSmartContract(TestContract::class);
$call = new EthereumCall(
    from: $wallet->getCoinbase(),
    maxPriorityFeePerGas: Ethereum::Gwei->toWei(21)
);
$contract->deploy(new DeployParams($call));

// Contract address after deploying
$address = $contract->getAddress();

// Send contract method
$contract->joke('EASY WEX pUA');

// Get result from contract method
$contract->getJoke(1);
<?php

use O21\CryptoWallets\Interfaces\ERC20\WalletInterface as IERC20Wallet;
use O21\CryptoWallets\Interfaces\EthereumWalletInterface;
use O21\CryptoWallets\Interfaces\WalletInterface as IWallet;
use O21\CryptoWallets\Units\Ethereum;

if (! function_exists('from_wei_to_eth')) {
    function from_wei_to_eth($number): string
    {
        return Ethereum::Ether->fromWei($number);
    }
}

if (! function_exists('wallet_value')) {
    /**
     * Returns value of wallet in basic wallet unit
     *
     * @param  \O21\CryptoWallets\Interfaces\WalletInterface  $wallet
     * @param $value
     * @return string
     */
    function wallet_value(IWallet $wallet, $value): string
    {
        if ($wallet instanceof IERC20Wallet) {
            return $wallet->fromWeiToToken($value);
        }

        if ($wallet instanceof EthereumWalletInterface) {
            return from_wei_to_eth($value);
        }

        return $value;
    }
}
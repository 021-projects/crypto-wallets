<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum;

use O21\CryptoWallets\Interfaces\ERC20\TokenContractInterface;
use O21\CryptoWallets\Models\EthereumCall;

abstract class AbstractERC20SmartContract extends AbstractSmartContract implements TokenContractInterface
{
    /**
     * @return string in token unit
     */
    public function totalSupply(): string
    {
        return first($this->call('totalSupply'));
    }

    /**
     * @param  string  $owner
     * @return string in token unit
     */
    public function balanceOf(string $owner): string
    {
        return first($this->call('balanceOf', [$owner]));
    }

    public function allowance(string $owner, string $spender): string
    {
        return first($this->call('allowance', [$owner, $spender]));
    }

    /**
     * @param  string  $spender
     * @param  string  $amount  in wei
     * @return string
     */
    public function approve(string $spender, string $amount): string
    {
        return $this->send('approve', [$spender, (int)$amount]);
    }

    /**
     * @param  string  $to
     * @param  string|int  $amount  in wei
     * @param  \O21\CryptoWallets\Models\EthereumCall|null  $call
     * @return string|null
     */
    public function transfer(
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string {
        return $this->send(
            'transfer',
            [$to, (int)$amount],
            $call
        );
    }

    /**
     * @param  string  $from
     * @param  string  $to
     * @param  string|int  $amount  in wei
     * @param  \O21\CryptoWallets\Models\EthereumCall|null  $call
     * @return string|null
     */
    public function transferFrom(
        string $from,
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string {
        return $this->send(
            'transferFrom',
            [$from, $to, (int)$amount],
            $call
        );
    }

    /**
     * @param  string  $from
     * @param  string  $to
     * @param  string|int  $amount  in wei
     * @param  \O21\CryptoWallets\Models\EthereumCall|null  $call
     * @return string|null
     */
    public function estimateTransferFromGas(
        string $from,
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string {
        return $this->estimateGas(
            'transferFrom',
            [$from, $to, (int)$amount],
            $call
        );
    }

    /**
     * @param  string  $to
     * @param  string|int  $amount  in wei
     * @param  \O21\CryptoWallets\Models\EthereumCall|null  $call
     * @return string|null
     */
    public function estimateTransferGas(
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string {
        return $this->estimateGas(
            'transfer',
            [$to, (int)$amount],
            $call
        );
    }
}
<?php

namespace O21\CryptoWallets\Interfaces\ERC20;

use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\CryptoWallets\Models\EthereumCall;

interface TokenContractInterface extends SmartContractInterface
{
    public function totalSupply(): string;

    public function balanceOf(string $owner): string;

    public function allowance(string $owner, string $spender): string;

    public function transfer(
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string;

    public function estimateTransferGas(
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string;

    public function transferFrom(
        string $from,
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string;

    public function estimateTransferFromGas(
        string $from,
        string $to,
        string|int $amount,
        ?EthereumCall $call = null
    ): ?string;

    // todo add more methods
}
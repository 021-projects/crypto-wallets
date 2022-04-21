<?php

namespace O21\CryptoWallets\Interfaces;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Models\EthereumBlock;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use Web3\Eth;

interface EthereumWalletInterface
{
    public function getBlock(string|int $hashOrNumber, bool $fullTransactions = false): EthereumBlock;

    public function getAccounts(): array;

    public function getLastBlockNumber(): int;

    public function getSmartContract(string $class): SmartContractInterface;

    /**
     * @param  string|int  $blockNumber
     * @param  string|int|null  $lastBlockNumber
     * @return \Illuminate\Support\Collection<\O21\CryptoWallets\Models\EthereumTransaction>
     */
    public function getTransactionsInBlock(
        string|int $blockNumber,
        string|int|null $lastBlockNumber = null
    ): Collection;

    public function getTransactionReceipt(string $hash): ?EthereumTransactionReceipt;

    public function getCoinbase(): string;

    public function getEth(): Eth;

    public function ethCall(string $method, array $params = [], bool $single = true): mixed;

    public function personalCall(string $method, array $params = [], bool $single = true): mixed;
}
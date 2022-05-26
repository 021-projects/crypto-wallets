<?php

namespace O21\CryptoWallets\Interfaces;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;

interface SmartContractInterface
{
    public function __construct(EthereumWalletInterface $wallet);

    public function deploy(
        DeployParams $params,
        string &$error = null
    ): ?EthereumTransactionReceipt;

    public function decodeData(string $data, array $topics): array;

    /**
     * @return \Illuminate\Support\Collection<\O21\Support\FreeObject>
     */
    public function getLogs(): Collection;

    public function getAddress(): ?string;

    public function setAddress(?string $address): SmartContractInterface;

    public function call(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        bool $single = true
    ): mixed;

    public function send(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        ?string &$error = null
    ): ?string;

    public function estimateGas(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        ?string &$error = null
    ): ?string;

    public static function getAbi(): array;

    public static function getByteCode(): string;
}
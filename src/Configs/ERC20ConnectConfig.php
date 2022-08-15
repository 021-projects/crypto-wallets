<?php

namespace O21\CryptoWallets\Configs;

use O21\CryptoWallets\Interfaces\ERC20\ConnectConfigInterface;

class ERC20ConnectConfig extends ConnectConfig implements ConnectConfigInterface
{
    public function __construct(
        protected string $user = '',
        protected string $password = '',
        protected string $host = 'localhost',
        protected int $port = 8332,
        protected string $scheme = 'http',
        protected string $walletName = '',
        protected string $contractAddress = '',
        protected int $tokenDecimals = 6
    ) { }

    public function getContractAddress(): string
    {
        return $this->contractAddress;
    }

    public function getTokenDecimals(): int
    {
        return $this->tokenDecimals;
    }

    public function setContractAddress(string $value): ConnectConfigInterface
    {
        $this->contractAddress = $value;
        return $this;
    }

    public function setTokenDecimals(int $value): ConnectConfigInterface
    {
        $this->tokenDecimals = $value;
        return $this;
    }
}
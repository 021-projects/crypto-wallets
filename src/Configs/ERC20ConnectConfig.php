<?php

namespace O21\CryptoWallets\Configs;

use O21\CryptoWallets\Interfaces\ERC20\ConnectConfigInterface;

class ERC20ConnectConfig extends ConnectConfig implements ConnectConfigInterface
{
    public function __construct(
        public string $user = '',
        public string $password = '',
        public string $host = 'localhost',
        public int $port = 8332,
        public string $scheme = 'http',
        public string $walletName = '',
        public string $contractAddress = '',
        public int $tokenDecimals = 6
    ) { }

    public function getContractAddress(): string
    {
        return $this->contractAddress;
    }

    public function getTokenDecimals(): int
    {
        return $this->tokenDecimals;
    }
}
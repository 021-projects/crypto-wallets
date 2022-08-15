<?php

namespace O21\CryptoWallets\Interfaces\ERC20;

interface ConnectConfigInterface extends \O21\CryptoWallets\Interfaces\ConnectConfigInterface
{
    public function __construct(
        string $user = '',
        string $password = '',
        string $host = 'localhost',
        int $port = 8332,
        string $scheme = 'http',
        string $walletName = '',
        string $contractAddress = '',
        int $tokenDecimals = 6
    );

    public function getContractAddress(): string;

    public function getTokenDecimals(): int;
}
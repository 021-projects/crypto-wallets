<?php

namespace O21\CryptoWallets\Interfaces;

use Illuminate\Contracts\Support\Arrayable;

interface ConnectConfigInterface extends Arrayable
{
    public function __construct(
        string $user = '',
        string $password = '',
        string $host = 'localhost',
        int $port = 8332,
        string $scheme = 'http',
        string $walletName = ''
    );

    public function toUrl(): string;

    public function getUser(): string;

    public function getPassword(): string;

    public function getHost(): string;

    public function getPort(): int;

    public function getScheme(): string;

    public function getWalletName(): string;
}
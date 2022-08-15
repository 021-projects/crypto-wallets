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
    public function setUser(string $value): ConnectConfigInterface;

    public function getPassword(): string;
    public function setPassword(string $value): ConnectConfigInterface;

    public function getHost(): string;
    public function setHost(string $value): ConnectConfigInterface;

    public function getPort(): int;
    public function setPort(int $value): ConnectConfigInterface;

    public function getScheme(): string;
    public function setScheme(string $value): ConnectConfigInterface;

    public function getWalletName(): string;
    public function setWalletName(string $value): ConnectConfigInterface;
}
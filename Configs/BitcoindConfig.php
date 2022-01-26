<?php

namespace O21\CryptoWallets\Configs;

class BitcoindConfig
{
    public static function fill(
        string $user,
        string $password,
        string $host = 'localhost',
        int $port = 8332,
        string $scheme = 'http'
    ): array {
        return compact('user', 'password', 'host', 'port', 'scheme');
    }
}
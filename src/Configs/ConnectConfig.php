<?php

namespace O21\CryptoWallets\Configs;

use O21\CryptoWallets\Interfaces\ConnectConfigInterface;

class ConnectConfig implements ConnectConfigInterface
{
    public function __construct(
        public string $user = '',
        public string $password = '',
        public string $host = 'localhost',
        public int $port = 8332,
        public string $scheme = 'http',
        public string $walletName = ''
    ) { }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'scheme' => $this->scheme,
        ];
    }

    public function toUrl(): string
    {
        return "$this->scheme://{$this->getLoginPart()}$this->host:$this->port";
    }

    protected function getLoginPart(): string
    {
        if ($this->user && $this->password) {
            return "$this->user:$this->password@";
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getWalletName(): string
    {
        return $this->walletName;
    }
}
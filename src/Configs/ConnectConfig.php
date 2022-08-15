<?php

namespace O21\CryptoWallets\Configs;

use O21\CryptoWallets\Interfaces\ConnectConfigInterface;

class ConnectConfig implements ConnectConfigInterface
{
    public function __construct(
        protected string $user = '',
        protected string $password = '',
        protected string $host = 'localhost',
        protected int $port = 8332,
        protected string $scheme = 'http',
        protected string $walletName = ''
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

    public function setUser(string $value): ConnectConfigInterface
    {
        $this->user = $value;
        return $this;
    }

    public function setPassword(string $value): ConnectConfigInterface
    {
        $this->password = $value;
        return $this;
    }

    public function setHost(string $value): ConnectConfigInterface
    {
        $this->host = $value;
        return $this;
    }

    public function setPort(int $value): ConnectConfigInterface
    {
        $this->port = $value;
        return $this;
    }

    public function setScheme(string $value): ConnectConfigInterface
    {
        $this->scheme = $value;
        return $this;
    }

    public function setWalletName(string $value): ConnectConfigInterface
    {
        $this->walletName = $value;
        return $this;
    }
}
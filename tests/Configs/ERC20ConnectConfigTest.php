<?php

namespace Tests\Configs;

use O21\CryptoWallets\Configs\ERC20ConnectConfig;
use Tests\TestCase;

class ERC20ConnectConfigTest extends TestCase
{
    protected ERC20ConnectConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new ERC20ConnectConfig(
            $_ENV['ETH_USER'],
            $_ENV['ETH_PASSWORD'],
            $_ENV['ETH_HOST'],
            $_ENV['ETH_PORT'],
            contractAddress: '0x123',
            tokenDecimals: 2
        );
    }

    public function testSettersAndGetters(): void
    {
        $this->config->setUser('user');
        $this->config->setPassword('password');
        $this->config->setHost('host');
        $this->config->setPort(123);
        $this->config->setScheme('scheme');
        $this->config->setWalletName('walletName');
        $this->config->setContractAddress('0x123456789');
        $this->config->setTokenDecimals(21);

        $this->assertEquals('user', $this->config->getUser());
        $this->assertEquals('password', $this->config->getPassword());
        $this->assertEquals('host', $this->config->getHost());
        $this->assertEquals(123, $this->config->getPort());
        $this->assertEquals('scheme', $this->config->getScheme());
        $this->assertEquals('walletName', $this->config->getWalletName());
        $this->assertEquals('0x123456789', $this->config->getContractAddress());
        $this->assertEquals(21, $this->config->getTokenDecimals());
    }

    public function testToArrayStructure(): void
    {
        $this->assertEquals(
            [
                'user' => $_ENV['ETH_USER'],
                'password' => $_ENV['ETH_PASSWORD'],
                'host' => $_ENV['ETH_HOST'],
                'port' => (int)$_ENV['ETH_PORT'],
                'scheme' => 'http',
            ],
            $this->config->toArray()
        );
    }
}

<?php

namespace Tests\SmartContracts\Ethereum;

use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;
use O21\CryptoWallets\Units\Ethereum;
use PHPUnit\Framework\TestCase;

class DeployParamsTest extends TestCase
{
    public function testSet(): void
    {
        $params = new DeployParams('0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea');
        $params->set('foo', 'bar');
        $params->set([
            'whiskey' => 'cola'
        ]);

        $this->assertEquals([
            'from' => '0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea',
            'foo' => 'bar',
            'whiskey' => 'cola'
        ], $params->toArray());
    }

    public function testCall(): void
    {
        $call = new EthereumCall(
            maxPriorityFeePerGas: Ethereum::Gwei->toWei(21)
        );

        $params = new DeployParams('0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea');
    }
}

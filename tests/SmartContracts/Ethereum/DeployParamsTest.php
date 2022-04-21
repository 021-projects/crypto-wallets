<?php

namespace Tests\SmartContracts\Ethereum;

use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;
use O21\CryptoWallets\Units\Ethereum;
use PHPUnit\Framework\TestCase;

class DeployParamsTest extends TestCase
{
    public function testToArrayStructure(): void
    {
        $call = new EthereumCall(
            from: '0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea',
            maxPriorityFeePerGas: Ethereum::Gwei->toWei(21)
        );

        $params = new DeployParams($call, ['something']);

        $this->assertEquals([
            'something',
            [
                'from' => '0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea',
                'maxPriorityFeePerGas' => '0x4e3b29200'
            ]
        ], $params->toArray());
    }
}

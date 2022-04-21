<?php

namespace Tests\Estimates;

use O21\CryptoWallets\Fees\EthereumFee;
use Tests\TestCase;

class EthereumFeeTest extends TestCase
{
    public function testToArrayStructure(): void
    {
        $fee = new EthereumFee('1000000000000000000', 60);
        $this->assertEquals([
            'value' => [
                'wei' => '1000000000000000000',
                'gwei' => '1000000000',
                'ether' => '1'
            ],
            'approximate_time' => [
                'seconds' => 60,
                'minutes' => 1,
                'hours' => 0
            ]
        ], $fee->toArray());
    }
}

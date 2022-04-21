<?php

namespace Tests\Estimates;

use O21\CryptoWallets\Fees\BitcoindFee;
use Tests\TestCase;

class BitcoindFeeTest extends TestCase
{
    protected BitcoindFee $fee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fee = new BitcoindFee('0.00001000', 60);
    }

    public function testToArrayStructure(): void
    {
        $this->assertEquals([
            'blocks' => 10,
            'value_per_kbyte' => '0.00001000',
            'value_per_byte' => '0.00000001000',
            'value_per_byte_in_satoshi' => '1.00000000000',
            'approximate_time' => [
                'seconds' => 60,
                'minutes' => 1,
                'hours' => 0
            ]
        ], $this->fee->toArray());
    }

    public function testGetBlocks(): void
    {
        $this->assertEquals(10, $this->fee->getBlocks());
    }
}

<?php

namespace Tests\RateProviders;

use O21\CryptoWallets\RateProviders\BinanceProvider;
use Tests\TestCase;

class BinanceProviderTest extends TestCase
{
    protected BinanceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new BinanceProvider();
    }

    public function testHistory(): void
    {
        $history = $this->provider->history('USD');

        $this->assertNotEmpty($history);
        $this->assertCount(60, $history);
    }

    public function testGetRate(): void
    {
        $rate = $this->provider->getRate('USD');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    public function testIsSupportsHistory(): void
    {
        $this->assertNotFalse($this->provider->isSupportsHistory());
    }
}

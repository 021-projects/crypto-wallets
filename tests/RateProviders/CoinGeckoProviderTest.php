<?php

namespace Tests\RateProviders;

use O21\CryptoWallets\RateProviders\CoinGeckoProvider;
use Tests\TestCase;

class CoinGeckoProviderTest extends TestCase
{
    protected CoinGeckoProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new CoinGeckoProvider();
    }

    public function testGetRate(): void
    {
        $rate = $this->provider->getRate('USD', 'ETH');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }
}
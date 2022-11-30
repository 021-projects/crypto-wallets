<?php

namespace Tests\RateProviders;

use O21\CryptoWallets\RateProviders\CoinApiProvider;
use Tests\TestCase;

class CoinApiProviderTest extends TestCase
{
    protected CoinApiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new CoinApiProvider();
    }

    public function testGetRate(): void
    {
        $rate = $this->provider->getRate('USD', 'LTC');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }
}
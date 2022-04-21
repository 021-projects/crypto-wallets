<?php

namespace Tests\RateProviders\Bitcoin;

use O21\CryptoWallets\Exceptions\RateProviderDoesntSupportsHistory;
use O21\CryptoWallets\RateProviders\Bitcoin\BlockchainProvider;
use PHPUnit\Framework\TestCase;

class BlockchainProviderTest extends TestCase
{
    protected BlockchainProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new BlockchainProvider();
    }

    public function testGetRates(): void
    {
        $rates = $this->provider->getRates(['USD', 'EUR']);

        $this->assertIsArray($rates);
        $this->assertNotEmpty($rates);
        $this->assertCount(2, $rates);

        $this->assertArrayHasKey('USD', $rates);
        $this->assertArrayHasKey('EUR', $rates);

        $this->assertGreaterThan(0, $rates['USD']);
        $this->assertGreaterThan(0, $rates['EUR']);
    }

    public function testGetRate(): void
    {
        $rate = $this->provider->getRate('USD');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }

    public function testIsSupportsHistory(): void
    {
        $this->assertNotTrue($this->provider->isSupportsHistory());
    }

    public function testRateProviderDoesntSupportsHistoryException(): void
    {
        $this->expectException(RateProviderDoesntSupportsHistory::class);
        $this->provider->history('USD');
    }
}

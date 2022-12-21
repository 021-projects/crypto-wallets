<?php

namespace Rates;

use O21\CryptoWallets\Rates\CoinGeckoProvider;
use PHPUnit\Framework\TestCase;

class CoinGeckoProviderTest extends TestCase
{
    public function testGetRate()
    {
        $provider = new CoinGeckoProvider();
        $rate = $provider->getRate('RUB', 'BTC');

        $this->assertIsFloat($rate);
        $this->assertGreaterThan(0, $rate);
    }
}

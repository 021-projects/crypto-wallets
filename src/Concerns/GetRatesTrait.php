<?php

namespace O21\CryptoWallets\Concerns;

use O21\CryptoWallets\Exceptions\RateProviderDoesntSupportsHistory;
use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;
use O21\CryptoWallets\Units\RateInterval;

trait GetRatesTrait
{
    public function getRate(
        string $currency = 'USD',
        ?RateProvider $provider = null
    ): float {
        $rate = $this->getRateProvider($provider)
            ->getRate($currency, $this->getSymbol());

        return crypto_number($rate);
    }

    public function getBestRate(
        string $currency = 'USD',
        int $limit = null,
        RateInterval $interval = RateInterval::Minutes,
        ?RateProvider $provider = null
    ): float {
        $limit ??= $this->getDefaultBestRateLimit();

        $rateProvider = $this->getRateProvider($provider);

        throw_unless($rateProvider->isSupportsHistory(), RateProviderDoesntSupportsHistory::class);

        return max($rateProvider->history($currency, $this->getSymbol(), $limit, $interval));
    }
}
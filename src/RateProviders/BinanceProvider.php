<?php

namespace O21\CryptoWallets\RateProviders;

use GuzzleHttp\Utils;
use Illuminate\Support\Arr;
use O21\CryptoWallets\RateProviders\Concerns\HasApiTrait;
use Binance\API;
use O21\CryptoWallets\Units\RateInterval;

class BinanceProvider extends AbstractRateProvider
{
    use HasApiTrait {
        __construct as bindGuzzle;
    }

    private const API_V3_URL = 'https://api.binance.com/api/v3/';

    protected API $api;

    public function __construct()
    {
        $this->bindGuzzle();
        $this->api = new API(null, null);
    }

    public function isSupportsHistory(): bool
    {
        return true;
    }

    public function getRate(string $toSymbol, string $fromSymbol = ''): float
    {
        $response = $this->averagePrice($this->getQuerySymbol($fromSymbol, $toSymbol));
        return (float)Arr::get($response, 'price', 0);
    }

    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 60,
        RateInterval $interval = RateInterval::Minutes
    ): array {
        $symbol = $this->getQuerySymbol($fromSymbol, $toSymbol);
        $queryInterval = $this->getQueryInterval($interval);

        $lines = $this->klines($symbol, $queryInterval, $limit);

        return $this->floatMap($this->getPricesFromLines($lines));
    }

    /**
     * Response example: https://github.com/binance-exchange/binance-official-api-docs/blob/master/rest-api.md#klinecandlestick-data
     * Returns close prices
     *
     * @param array $lines
     * @return array
     */
    protected function getPricesFromLines(array $lines): array
    {
        return array_combine(
            array_map(static fn($v) => round($v / 1000), array_column($lines, 6)),
            array_column($lines, 4)
        );
    }

    protected function averagePrice(string $symbol): array
    {
        return Utils::jsonDecode(
            $this->client->get(self::API_V3_URL.'avgPrice', [
                'timeout' => 3, // Response timeout
                'connect_timeout' => 3, // Connection timeout,
                'query' => compact('symbol')
            ])->getBody(),
            true
        );
    }

    protected function klines(string $symbol, string $interval, int $limit = 500): array
    {
        return Utils::jsonDecode(
            $this->client->get(self::API_V3_URL.'klines', [
                'timeout' => 3, // Response timeout
                'connect_timeout' => 3, // Connection timeout,
                'query' => compact('symbol', 'interval', 'limit')
            ])->getBody(),
            true
        );
    }

    protected function getQuerySymbol(string $fromSymbol, string $toSymbol): string
    {
        return $this->usdtReplace($fromSymbol ?: 'BTC') . $this->usdtReplace($toSymbol);
    }

    protected function usdtReplace(string $symbol): string
    {
        return $symbol === 'USD' ? 'USDT' : $symbol;
    }

    protected function getQueryInterval(RateInterval $interval): string
    {
        return match ($interval) {
            RateInterval::Minutes => '1m',
            RateInterval::Hours => '1h',
            RateInterval::Days => '1d',
        };
    }
}

<?php

namespace O21\CryptoWallets\Rates;

use O21\CryptoWallets\Utils;
use O21\CryptoWallets\Rates\Concerns\HasApi;
use Binance\API;

class Binance extends AbstractRate
{
    use HasApi {
        __construct as apiConstruct;
    }

    const API_V3_URL = 'https://api.binance.com/api/v3/';

    protected API $api;

    public function __construct()
    {
        $this->apiConstruct();
        $this->api = new API(null, null);
    }

    public function isSupportsHistory(): bool
    {
        return true;
    }

    public function getRate(string $toSymbol, string $fromSymbol = ''): float
    {
        return (float)$this->safeLoad(
            fn() => $this->averagePrice($this->defineSymbol($fromSymbol, $toSymbol))['price']
        );
    }

    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 100,
        int $interval = self::INTERVAL_HOURS
    ): array {
        $symbol = $this->defineSymbol($fromSymbol, $toSymbol);
        $apiInterval = $this->defineInterval($interval);

        $lines = $this->safeLoad(fn() => $this->klines($symbol, $apiInterval, $limit));

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

    protected function averagePrice(string $symbol)
    {
        return \GuzzleHttp\json_decode(
            $this->client->get(self::API_V3_URL.'avgPrice', [
                'timeout' => 3, // Response timeout
                'connect_timeout' => 3, // Connection timeout,
                'query' => compact('symbol')
            ])->getBody(),
            true
        );
    }

    protected function klines(string $symbol, string $interval, int $limit = 500)
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

    protected function defineSymbol(string $fromSymbol, string $toSymbol)
    {
        return $this->usdtReplace($fromSymbol ?: 'BTC') . $this->usdtReplace($toSymbol);
    }

    protected function usdtReplace(string $symbol)
    {
        return $symbol === 'USD' ? 'USDT' : $symbol;
    }

    protected function defineInterval(int $interval)
    {
        switch ($interval) {
            case self::INTERVAL_MINUTES:
                return '1m';

            case self::INTERVAL_HOURS:
            default:
                return '1h';

            case self::INTERVAL_DAYS:
                return '1d';
        }
    }
}

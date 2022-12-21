<?php

namespace O21\CryptoWallets\Rates;

use O21\CryptoWallets\Utils;
use Illuminate\Support\Arr;
use O21\CryptoWallets\Rates\Concerns\HasApi;

class CoinGeckoProvider extends AbstractRate
{
    use HasApi;

    private const API_URL = 'https://api.coingecko.com/api/v3/';

    private const COIN_SYMBOLS_ID_MAP = [
        'BTC' => 'bitcoin',
        'ETH' => 'ethereum',
        'LTC' => 'litecoin',
        'BCH' => 'bitcoin-cash',
        'XRP' => 'ripple',
        'USDT' => 'tether',
        'BNB' => 'binancecoin',
        'ADA' => 'cardano',
        'DOGE' => 'dogecoin',
        'DOT' => 'polkadot',
        'XLM' => 'stellar',
        'UNI' => 'uniswap',
        'LINK' => 'chainlink',
        'USDC' => 'usd-coin',
        'WBTC' => 'wrapped-bitcoin',
    ];

    public function getRate(string $toSymbol, string $fromSymbol = 'BTC'): float
    {
        $fromSymbolId = $this->getFromSymbolId($fromSymbol);
        $response = Utils::jsonDecode(
            $this->client->get(
                self::API_URL . 'simple/price',
                [
                    'query' => [
                        'ids' => $fromSymbolId,
                        'vs_currencies' => $toSymbol,
                    ]
                ]
            )->getBody()->getContents(),
            true
        );
        return (float)Arr::get($response, mb_strtolower("$fromSymbolId.$toSymbol"), 0);
    }

    protected function getFromSymbolId(string $fromSymbol): string
    {
        $fromSymbolKey = mb_strtoupper($fromSymbol);
        if (isset(self::COIN_SYMBOLS_ID_MAP[$fromSymbolKey])) {
            $fromSymbol = self::COIN_SYMBOLS_ID_MAP[$fromSymbolKey];
        }

        return $fromSymbol;
    }
}
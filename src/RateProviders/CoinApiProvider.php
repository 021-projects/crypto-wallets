<?php

namespace O21\CryptoWallets\RateProviders;

use GuzzleHttp\Utils;
use Illuminate\Support\Arr;
use O21\CryptoWallets\RateProviders\Concerns\HasApiTrait;

class CoinApiProvider extends AbstractRateProvider
{
    use HasApiTrait {
        __construct as bindGuzzle;
    }

    private const API_URL = 'https://rest.coinapi.io/v1/';

    public function __construct(
        protected ?string $apiKey = ''
    ) {
        if (empty($this->apiKey)) {
            $this->apiKey = env('COIN_API_IO_KEY');
        }
        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('CoinApi.io API key is not set.');
        }
        $this->bindGuzzle();
    }

    public function getRate(string $toSymbol, string $fromSymbol = 'BTC'): float
    {
        $response = Utils::jsonDecode(
            $this->client->get(
                self::API_URL . 'exchangerate/' . $this->getQuerySymbol($fromSymbol, $toSymbol),
                [
                    'headers' => [
                        'X-CoinAPI-Key' => $this->apiKey
                    ]
                ]
            )->getBody()->getContents(),
            true
        );
        return (float)Arr::get($response, 'rate', 0);
    }

    protected function getQuerySymbol(string $fromSymbol, string $toSymbol): string
    {
        return $fromSymbol . '/' . $toSymbol;
    }
}
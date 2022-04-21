<?php

namespace O21\CryptoWallets\RateProviders\Bitcoin;

use GuzzleHttp\Utils;
use O21\CryptoWallets\Exceptions\BlockchainReturnedEmptyResponseException;
use O21\CryptoWallets\RateProviders\Concerns\HasApiTrait;
use O21\CryptoWallets\RateProviders\AbstractRateProvider;
use Illuminate\Support\Arr;

class BlockchainProvider extends AbstractRateProvider
{
    use HasApiTrait;

    private const API_URL = 'https://blockchain.info/ticker';

    public function getRate(string $toSymbol, string $fromSymbol = ''): float
    {
        return Arr::get($this->rates(), $toSymbol);
    }

    public function getRates(array $symbols): array
    {
        return Arr::only($this->rates(), $symbols);
    }

    protected function rates(): array
    {
        return $this->getPreparedRates();
    }

    private function getPreparedRates(): array
    {
        $rates = Utils::jsonDecode(
            $this->client->get(self::API_URL, [
                'timeout' => 3, // Response timeout
                'connect_timeout' => 3, // Connection timeout
            ])->getBody(),
            true
        );

        throw_if(empty($rates), BlockchainReturnedEmptyResponseException::class);

        return $this->floatMap($rates, 'buy');
    }
}

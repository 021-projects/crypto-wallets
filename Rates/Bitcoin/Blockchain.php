<?php

namespace O21\CryptoWallets\Rates\Bitcoin;

use O21\CryptoWallets\Utils;
use O21\CryptoWallets\Rates\Concerns\HasApi;
use O21\CryptoWallets\Rates\AbstractRate;
use Illuminate\Support\Arr;

class Blockchain extends AbstractRate
{
    use HasApi;

    const API_URL = 'https://blockchain.info/ticker';

    public function getRate(string $toSymbol, string $fromSymbol = ''): float
    {
        return Arr::get($this->rates(), $toSymbol);
    }

    public function getRates(array $symbols): array
    {
        return Arr::only($this->rates(), $symbols);
    }

    protected function rates()
    {
        return $this->safeLoad(fn() => $this->getPreparedRates());
    }

    private function getPreparedRates()
    {
        $rates = Utils::jsonDecode(
            $this->client->get(self::API_URL, [
                'timeout' => 3, // Response timeout
                'connect_timeout' => 3, // Connection timeout
            ])->getBody(),
            true
        );

        if (empty($rates)) {
            throw new \RuntimeException('Failed download course data.');
        }

        return array_map(static fn($v) => (float)$v['buy'] ?? 0, $rates);
    }
}

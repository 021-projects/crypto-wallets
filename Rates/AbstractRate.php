<?php

namespace O21\CryptoWallets\Rates;

use O21\CryptoWallets\Contracts\WalletRate;

abstract class AbstractRate implements WalletRate
{
    public function isSupportsHistory(): bool
    {
        return false;
    }

    public function history(
        string $toSymbol,
        string $fromSymbol = '',
        int $limit = 100,
        int $interval = self::INTERVAL_HOURS
    ): array {
        throw new \LogicException('This rate handler does not support course history.');
    }

    /**
     * @throws \Exception
     */
    protected function safeLoad(\Closure $callback)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load course data.');
        }
    }

    protected function floatMap(array $array): array
    {
        return array_map(static fn($v) => (float)$v, $array);
    }
}

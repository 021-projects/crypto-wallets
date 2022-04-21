<?php

namespace O21\CryptoWallets\Concerns;

use Web3\Eth;

trait EthereumCallsTrait
{
    protected Eth $eth;

    public function ethCall(string $method, array $params = [], bool $single = true): mixed
    {
        return $this->namespaceCall($this->eth, $method, $params, $single);
    }

    protected function namespaceCall(
        $namespaceObj,
        string $method,
        array $params = [],
        bool $single = true
    ): mixed {
        $result = [];

        $params[] = static function ($err, ...$args) use (&$result) {
            if ($err !== null) {
                throw $err;
            }

            $result = $args;
        };

        $namespaceObj->$method(...$params);

        return $single ? first($result) : $result;
    }

    /**
     * @return \Web3\Eth
     */
    public function getEth(): Eth
    {
        return $this->eth;
    }
}
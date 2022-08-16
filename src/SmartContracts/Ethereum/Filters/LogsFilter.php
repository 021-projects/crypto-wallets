<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Web3\Utils;

class LogsFilter extends \stdClass implements Arrayable
{
    public function __construct(
        public ?string $fromBlock = null,
        public ?string $toBlock = null,
        public ?string $address = null,
        public ?array $topics = null,
        public ?string $blockhash = null
    ) {}

    public function toArray()
    {
        return array_filter([
            'fromBlock' => $this->toHex($this->fromBlock),
            'toBlock' => $this->toHex($this->toBlock),
            'address' => $this->address,
            'topics' => $this->topics,
            'blockhash' => $this->blockhash
        ]);
    }

    private function toHex($value): ?string
    {
        if (str_starts_with($value, '0x')) {
            return $value;
        }

        return $value ? Utils::toHex($value, true) : null;
    }
}
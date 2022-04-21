<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum\Filters;

use Illuminate\Contracts\Support\Arrayable;

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
            'fromBlock' => $this->fromBlock,
            'toBlock' => $this->toBlock,
            'address' => $this->address,
            'topics' => $this->topics,
            'blockhash' => $this->blockhash
        ]);
    }
}
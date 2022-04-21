<?php

namespace O21\CryptoWallets\Models;

use Illuminate\Contracts\Support\Arrayable;
use Web3\Utils;

class EthereumCall implements Arrayable
{
    public function __construct(
        public ?string $from = '',
        public ?string $to = '',
        public string|int|null $gas = null,
        public string|int|null $gasPrice = null,
        public string|int|null $maxPriorityFeePerGas = null,
        public string|int|null $maxFeePerGas = null,
        public string|int|null $value = null,
        public ?string $data = null
    ) { }

    public function toArray()
    {
        return array_filter([
            'from'                 => $this->from,
            'to'                   => $this->to,
            'gas'                  => $this->toHex($this->gas),
            'gasPrice'             => $this->toHex($this->gasPrice),
            'maxPriorityFeePerGas' => $this->toHex($this->maxPriorityFeePerGas),
            'maxFeePerGas'         => $this->toHex($this->maxFeePerGas),
            'value'                => $this->toHex($this->value),
            'data'                 => $this->data
        ]);
    }

    private function toHex($value): ?string
    {
        return $value ? Utils::toHex($value, true) : null;
    }
}
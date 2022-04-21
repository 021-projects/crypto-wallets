<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use O21\CryptoWallets\Exceptions\Ethereum\InvalidAddressException;
use O21\CryptoWallets\Models\EthereumCall;
use Web3\Validators\AddressValidator;

class DeployParams implements Arrayable
{
    private string $from;

    public function __construct(
        string $from,
        public ?EthereumCall $call = null,
        private array $_params = []
    ) {
        $this->from = $from;
        
        throw_unless(AddressValidator::validate($from), InvalidAddressException::class);
    }

    public function toArray(): array
    {
        $arr = array_merge(
            ['from' => $this->from],
            $this->_params
        );

        if ($this->call) {
            $arr[] = $this->call->toArray();
        }

        return $arr;
    }

    public function set($key, $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $_key => $val) {
                Arr::set($this->_params, $_key, $val);
            }
            return;
        }

        Arr::set($this->_params, $key, $value);
    }
}
<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum;

use Illuminate\Contracts\Support\Arrayable;
use O21\CryptoWallets\Exceptions\Ethereum\InvalidAddressException;
use O21\CryptoWallets\Models\EthereumCall;
use Web3\Validators\AddressValidator;

class DeployParams implements Arrayable
{
    public function __construct(
        public EthereumCall $call,
        public array $functionArguments = []
    ) {
        throw_unless(AddressValidator::validate($call->from), InvalidAddressException::class);
    }

    public function toArray(): array
    {
        return [
            ...array_values($this->functionArguments),
            $this->call->toArray()
        ];
    }
}
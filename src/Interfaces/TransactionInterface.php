<?php

namespace O21\CryptoWallets\Interfaces;

/**
 * @property-read string $hash
 * @property-read string $shortHash
 * @property-read string $address
 * @property-read \O21\CryptoWallets\Units\TransactionType $type
 * @property-read string $amount
 * @property-read int $confirmations
 * @property-read string|null $blockHash
 * @property-read int|null $blockNumber
 * @property-read \Carbon\Carbon $time
 */
interface TransactionInterface
{
    public function getShortHash(): string;

    public function haveConfirmations(int $confirmations): int;
}
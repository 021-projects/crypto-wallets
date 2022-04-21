<?php

namespace O21\CryptoWallets\Models;

use O21\CryptoWallets\Interfaces\TransactionInterface;
use O21\Support\FreeObject;

abstract class AbstractTransaction extends FreeObject implements TransactionInterface
{
    public function getShortHash(): string
    {
        return substr($this->hash, 0, 16) . '...';
    }

    public function haveConfirmations(int $confirmations): int
    {
        return $this->confirmations >= $confirmations;
    }
}
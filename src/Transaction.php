<?php

namespace O21\CryptoWallets;

use O21\Support\FreeObject;

/**
 * @property-read string $address
 * @property-read string $category
 * @property-read float $amount
 * @property-read string $label
 * @property-read int $vout
 * @property-read int $confirmations
 * @property-read bool $generated
 * @property-read string $blockhash
 * @property-read int $blackindex
 * @property-read \Carbon\Carbon $blocktime
 * @property-read string $txid
 * @property-read array $walletconflicts
 * @property-read \Carbon\Carbon $time
 * @property-read \Carbon\Carbon $timereceived
 * @property-read array|null $details
 *
 * @property-read string $main_address
 * @property-read int $details_count
 * @property-read string $short_txid
 */
class Transaction extends FreeObject
{
    protected array $dates = ['time', 'blocktime', 'timereceived'];

    public function getMainAddress(): string
    {
        if (! $this->address
            && $this->details_count
        ) {
            return data_get(first($this->details), 'address', '');
        }

        return $this->address;
    }

    public function getDetailsCount(): int
    {
        if (! is_array($this->details)) {
            return 0;
        }

        return count($this->details);
    }

    public function getShortTxid(): string
    {
        return substr($this->txid, 0, 15) . '...';
    }

    public function haveConfirmations(int $confirmations): int
    {
        return $this->confirmations >= $confirmations;
    }

    public function isReceive(): bool
    {
        return $this->category === 'receive';
    }
}

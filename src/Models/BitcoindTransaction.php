<?php

namespace O21\CryptoWallets\Models;

use Illuminate\Support\Arr;
use O21\CryptoWallets\Units\TransactionType;

/**
 * @property-read string $label
 * @property-read int $vout
 * @property-read \Carbon\Carbon|null $blockTime
 * @property-read array $walletConflicts
 * @property-read \Carbon\Carbon $timeReceived
 * @property-read array|null $details
 *
 * @property-read string $mainAddress
 * @property-read int $detailsCount
 */
class BitcoindTransaction extends AbstractTransaction
{
    protected array $dates = ['time', 'blockTime', 'timeReceived'];

    public static function fromRpcTransaction(array $tx): static
    {
        $firstDetail = ! empty($tx['details']) ? first($tx['details']) : [];

        $address = Arr::get($tx, 'address', Arr::get($firstDetail, 'address'));
        $amount = abs(Arr::get($tx, 'amount', Arr::get($firstDetail, 'amount', 0)));
        $label = Arr::get($tx, 'label', Arr::get($firstDetail, 'label', ''));
        $vout = Arr::get($tx, 'vout', Arr::get($firstDetail, 'vout'));

        return new BitcoindTransaction([
            'hash'             => Arr::get($tx, 'txid'),
            'address'          => $address,
            'type'             => self::defineRpcTransactionType($tx),
            'amount'           => $amount,
            'confirmations'    => Arr::get($tx, 'confirmations'),
            'block_hash'       => Arr::get($tx, 'blockhash'),
            'block_number'     => Arr::get($tx, 'blockindex'),
            'block_time'       => Arr::get($tx, 'blocktime'),
            'time'             => Arr::get($tx, 'time'),
            'time_received'    => Arr::get($tx, 'timereceived'),
            'label'            => $label,
            'vout'             => $vout,
            'details'          => Arr::get($tx, 'details', []),
            'wallet_conflicts' => Arr::get($tx, 'walletconflicts', [])
        ]);
    }

    public static function defineRpcTransactionType(array $tx): TransactionType
    {
        if (! empty($tx['category'])) {
            return TransactionType::from($tx['category']);
        }

        $firstDetail = ! empty($tx['details']) ? first($tx['details']) : [];
        if ($firstDetail) {
            return TransactionType::from($firstDetail['category']);
        }

        return TransactionType::Regular;
    }

    public function getMainAddress(): string
    {
        if (! $this->address
            && $this->detailsCount
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

    public function isReceive(): bool
    {
        return $this->type->is(TransactionType::Receive);
    }
}
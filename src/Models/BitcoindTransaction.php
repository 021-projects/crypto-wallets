<?php

namespace O21\CryptoWallets\Models;

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

        return new BitcoindTransaction([
            'hash'             => $tx['txid'],
            'address'          => $tx['address'] ?? $firstDetail['address'],
            'type'             => self::defineRpcTransactionType($tx),
            'amount'           => abs($tx['amount'] ?: $firstDetail['amount']),
            'confirmations'    => $tx['confirmations'],
            'block_hash'       => $tx['blockhash'] ?? null,
            'block_number'     => $tx['blockindex'] ?? null,
            'block_time'       => $tx['blocktime'] ?? null,
            'time'             => $tx['time'],
            'time_received'    => $tx['timereceived'],
            'label'            => $tx['label'] ?? $firstDetail['label'],
            'vout'             => $tx['vout'] ?? $firstDetail['vout'],
            'details'          => $tx['details'] ?? [],
            'wallet_conflicts' => $tx['walletconflicts'] ?? []
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
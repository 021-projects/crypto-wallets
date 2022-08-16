<?php

namespace O21\CryptoWallets\Models;

use O21\CryptoWallets\Units\TransactionType;

/**
 * @property-read array $accessList
 * @property-read string $from
 * @property-read string $to
 * @property-read string $blockHash
 * @property-read string $blockNumber
 * @property-read string $chainId
 * @property-read string $gas
 * @property-read string $gasPrice
 * @property-read string $maxFeePerGas
 * @property-read string $maxPriorityFeePerGas
 * @property-read string $nonce
 * @property-read string $r
 * @property-read string $s
 * @property-read string $v
 * @property-read string $type
 * @property-read string $input
 * @property-read int $transactionIndex
 */
class EthereumTransaction extends AbstractTransaction
{
    public static function fromRpcTransaction(\stdClass $tx, ?\stdClass $block, int $lastBlockNumber): static
    {
        return new static([
            'hash'                     => $tx->hash,
            'block_hash'               => $tx->blockHash ?? null,
            'block_number'             => isset($tx->blockNumber) ? hexdec($tx->blockNumber) : null,
            'access_list'              => $tx->accessList ?? [],
            'chain_id'                 => $tx->chainId ?? '',
            'from'                     => $tx->from,
            'to'                       => $tx->to ?: '',
            'address'                  => $tx->to ?: '',
            'amount'                   => hexdec($tx->value),
            'gas'                      => hexdec($tx->gas),
            'gas_price'                => hexdec($tx->gasPrice),
            'max_fee_per_gas'          => hexdec($tx->maxFeePerGas ?? 0),
            'max_priority_fee_per_gas' => hexdec($tx->maxPriorityFeePerGas ?? 0),
            'confirmations'            => $block
                                            ? $lastBlockNumber - hexdec($tx->blockNumber)
                                            : 0,
            'time'                     => $block?->timestamp ? hexdec($block->timestamp) : 0,
            'type'                     => self::defineRpcTransactionType($tx),
            'nonce'                    => hexdec($tx->nonce),
            'r'                        => $tx->r,
            's'                        => $tx->s,
            'v'                        => $tx->v,
            'input'                    => $tx->input,
            'transaction_index'        => $tx->transactionIndex ? hexdec($tx->transactionIndex) : null,
        ]);
    }

    public static function defineRpcTransactionType(\stdClass $tx): TransactionType
    {
        if (empty($tx->to)) {
            return TransactionType::Contract;
        }

        return TransactionType::Regular;
    }
}
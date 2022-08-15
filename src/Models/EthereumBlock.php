<?php

namespace O21\CryptoWallets\Models;

use O21\Support\FreeObject;

/**
 * @property-read int $baseFeePerGas
 * @property-read int $difficulty
 * @property-read string $extraData
 * @property-read int $gasLimit
 * @property-read int $gasUsed
 * @property-read string $hash
 * @property-read string $logsBloom
 * @property-read string $miner
 * @property-read string $mixHash
 * @property-read string $nonce
 * @property-read int $number
 * @property-read string $parentHash
 * @property-read string $receiptsRoot
 * @property-read string $sha3Uncles
 * @property-read int $size
 * @property-read string $stateRoot
 * @property-read \Carbon\Carbon $timestamp
 * @property-read int $totalDifficulty
 * @property-read \Illuminate\Support\Collection<\O21\CryptoWallets\Models\EthereumTransaction|string> $transactions
 * @property-read string $transactionsRoot
 * @property-read array $uncles
 */
class EthereumBlock extends FreeObject
{
    protected array $dates = ['timestamp'];

    public static function fromRpcBlock(\stdClass $block, int $lastBlockNumber): static
    {
        $transactions = collect($block->transactions);
        if ($transactions->isNotEmpty() && ! is_string($transactions->first())) {
            $transactions = $transactions->map(
                fn(\stdClass $tx) => EthereumTransaction::fromRpcTransaction($tx, $block, $lastBlockNumber)
            );
        }

        return new static([
            'base_fee_per_gas'  => hexdec($block->baseFeePerGas),
            'difficulty'        => hexdec($block->difficulty),
            'extra_data'        => $block->extraData,
            'gas_limit'         => hexdec($block->gasLimit),
            'gas_used'          => hexdec($block->gasUsed),
            'hash'              => $block->hash,
            'logs_bloom'        => $block->logsBloom,
            'miner'             => $block->miner,
            'mix_hash'          => $block->mixHash,
            'nonce'             => $block->nonce,
            'number'            => hexdec($block->number),
            'parent_hash'       => $block->parentHash,
            'receipts_root'     => $block->receiptsRoot,
            'sha3_uncles'       => $block->sha3Uncles,
            'size'              => hexdec($block->size),
            'state_root'        => $block->stateRoot,
            'timestamp'         => hexdec($block->timestamp),
            'total_difficulty'  => hexdec($block->totalDifficulty),
            'transactions'      => $transactions,
            'transactions_root' => $block->transactionsRoot,
            'uncles'            => $block->uncles
        ]);
    }
}
<?php

namespace O21\CryptoWallets\Models;

use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\Support\FreeObject;

/**
 * @property-read string $address
 * @property-read string $blockHash
 * @property-read string $blockNumber
 * @property-read array $data
 * @property-read int $logIndex
 * @property-read bool $removed
 * @property-read array $topics
 * @property-read string $transactionHash
 * @property-read int $transactionIndex
 */
class EthereumTransactionLog extends FreeObject
{
    public static function fromRpcLog(\stdClass $log, SmartContractInterface $contract): static
    {
        return new static([
            'address'          => $log->address,
            'block_hash'       => $log->blockHash,
            'block_number'     => hexdec($log->blockNumber),
            'data'             => $contract->decodeData($log->data, $log->topics),
            'log_index'        => hexdec($log->logIndex),
            'removed'          => $log->removed,
            'topics'           => $log->topics,
            'transactionHash'  => $log->transactionHash,
            'transactionIndex' => hexdec($log->transactionIndex)
        ]);
    }
}
<?php

namespace O21\CryptoWallets\Models;

use O21\CryptoWallets\Exceptions\Ethereum\SmartContractRequireException;
use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\CryptoWallets\Units\Ethereum\TransactionReceiptStatus;
use O21\Support\FreeObject;

/**
 * @property-read string $blockHash
 * @property-read int $blockNumber
 * @property-read string|null $contractAddress
 * @property-read int $cumulativeGasUsed
 * @property-read int $effectiveGasPrice
 * @property-read string $from
 * @property-read int $gasUsed
 * @property-read \Illuminate\Support\Collection<\O21\CryptoWallets\Models\EthereumTransactionLog> $logs
 * @property-read string $logsBloom
 * @property-read \O21\CryptoWallets\Units\Ethereum\TransactionReceiptStatus $status
 * @property-read string|null $to
 * @property-read string $transactionHash
 * @property-read int $transactionIndex
 * @property-read \O21\CryptoWallets\Units\TransactionType $type
 */
class EthereumTransactionReceipt extends FreeObject
{
    public static function fromRpcReceipt(
        \stdClass $receipt,
        ?SmartContractInterface $contract = null
    ): static {
        throw_if(
            $receipt->contractAddress
            && ! empty($receipt->logs)
            && ! $contract,
            SmartContractRequireException::class
        );

        $logs = collect(
            array_map(
                static fn(\stdClass $log) => EthereumTransactionLog::fromRpcLog($log, $contract),
                $receipt->logs
            )
        );

        return new static([
            'block_hash'          => $receipt->blockHash,
            'block_number'        => hexdec($receipt->blockNumber),
            'contract_address'    => $receipt->contractAddress,
            'cumulative_gas_used' => hexdec($receipt->cumulativeGasUsed),
            'effective_gas_price' => hexdec($receipt->effectiveGasPrice),
            'from'                => $receipt->from,
            'gas_used'            => hexdec($receipt->gasUsed),
            'logs'                => $logs,
            'logs_bloom'          => $receipt->logsBloom,
            'status'              => TransactionReceiptStatus::from($receipt->status),
            'to'                  => $receipt->to,
            'transaction_hash'    => $receipt->transactionHash,
            'transaction_index'   => hexdec($receipt->transactionIndex),
            'type'                => EthereumTransaction::defineRpcTransactionType($receipt)
        ]);
    }
}
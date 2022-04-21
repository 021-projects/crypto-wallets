<?php

namespace O21\CryptoWallets\Models;

use O21\Support\FreeObject;

/*
 * baseFeePerGas: 30585535,
  difficulty: 2,
  extraData: "0xd983010a10846765746888676f312e31372e368664617277696e000000000000b125a63646121c2176a7a532277e5c4c3cd7c9843b18ef80929ef3aced3914dd606bb53de1449ef66637e76163b6036664d4ccc7168eb5f64f3f09c972aa732101",
  gasLimit: 11200637,
  gasUsed: 21000,
  hash: "0x9fb4ef358d762553c493a768ab20c9fd81f4cf014df8bcf93a21647e4fd644f7",
  logsBloom: "0x00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000",
  miner: "0x0000000000000000000000000000000000000000",
  mixHash: "0x0000000000000000000000000000000000000000000000000000000000000000",
  nonce: "0x0000000000000000",
  number: 27,
  parentHash: "0xf080582975d3d54f84cfba1754dffd0c41beaa449c7975a8f272ac76e9c18060",
  receiptsRoot: "0xf78dfb743fbd92ade140711c8bbc542b5e307f0ab7984eff35d751969fe57efa",
  sha3Uncles: "0x1dcc4de8dec75d7aab85b567b6ccd41ad312451b948a7413f0a142fd40d49347",
  size: 731,
  stateRoot: "0xf399931f9669378a56a86737435d3e2742ec7a8858c43c31a06f45700d66cb78",
  timestamp: 1650476823,
  totalDifficulty: 55,
  transactions: ["0xb9c2e1f9f66bd3d83eb3954abb106aacd4f4c8bd412e9b7bd4f0d72e9739a0e5"],
  transactionsRoot: "0x632fef3cfaa45d88962f93900d3928c89eb408c30d7a29ba740a8ad8361b199a",
  uncles: []
 */

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
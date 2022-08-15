<?php

namespace O21\CryptoWallets\Models;

use Illuminate\Support\Arr;
use O21\CryptoWallets\Units\TransactionType;
use O21\Support\FreeObject;

/**
 * @property \O21\CryptoWallets\Units\TransactionType $type
 * @property string $from
 * @property string $to
 * @property int $value
 */
class ERC20ReceiptInfo extends FreeObject
{
    // Transfer(address,address,uint256) in keccak-256
    public const TRANSFER_TOPIC_IN_KECCAK_256 = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
    // Approval(address,address,uint256) in keccak-256
    public const APPROVAL_TOPIC_IN_KECCAK_256 = '0x8c5be1e5ebec7d5bd14f71427d1e84f3dd0314c0f7b2291e5b200ac8c7c3b925';

    public static function fromRpcLog(EthereumTransactionLog $log): static
    {
        return new static([
            'type'  => match (Arr::get($log->topics, 0)) {
                self::TRANSFER_TOPIC_IN_KECCAK_256 => TransactionType::ERC20_Transfer,
                self::APPROVAL_TOPIC_IN_KECCAK_256 => TransactionType::ERC20_Approval,
                default                            => TransactionType::ERC20_AnotherEvent
            },
            'from'  => Arr::get($log->data, 'from', ''),
            'to'    => Arr::get($log->data, 'to', ''),
            'value' => (int)(string)Arr::get($log->data, 'value', 0)
        ]);
    }
}
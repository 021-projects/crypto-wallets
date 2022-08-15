<?php

namespace O21\CryptoWallets\Models;

use O21\CryptoWallets\Interfaces\SmartContractInterface;

/**
 * @property \O21\CryptoWallets\Models\ERC20ReceiptInfo|null $erc20Info
 */
class ERC20EthereumTransactionReceipt extends EthereumTransactionReceipt
{
    public static function fromRpcReceipt(\stdClass $receipt, ?SmartContractInterface $contract = null): static
    {
        $txReceipt = parent::fromRpcReceipt($receipt, $contract);

        $firstLog = $txReceipt->logs->first();
        $txReceipt->offsetSet(
            'erc20_info',
            $firstLog ? ERC20ReceiptInfo::fromRpcLog($firstLog) : null
        );

        return $txReceipt;
    }
}
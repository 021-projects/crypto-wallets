<?php

namespace O21\CryptoWallets\Models;

/**
 * @property \O21\CryptoWallets\Models\ERC20EthereumTransactionReceipt $erc20Receipt
 * @property \O21\CryptoWallets\Models\ERC20ReceiptInfo|null $erc20Info
 */
class ERC20EthereumTransaction extends EthereumTransaction
{
    public static function fromRpcTransactionWithReceipt(
        \stdClass $tx,
        ?\stdClass $block,
        int $lastBlockNumber,
        ERC20EthereumTransactionReceipt $receipt
    ): static {
        $newTx = parent::fromRpcTransaction($tx, $block, $lastBlockNumber);

        $newTx->offsetSet('erc20_info', $receipt->erc20Info);

        return $newTx;
    }
}
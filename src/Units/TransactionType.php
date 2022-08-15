<?php

namespace O21\CryptoWallets\Units;

enum TransactionType: string
{
    case Send = 'send';
    case Receive = 'receive';
    case Generate = 'generate';
    case Immature = 'immature';
    case Orphan = 'orphan';

    case Regular = 'regular';
    case Contract = 'contract';

    case ERC20_Transfer = 'erc20_transfer';
    case ERC20_Approval = 'erc20_approval';
    case ERC20_AnotherEvent = 'erc20_another_event';

    public function is(TransactionType $type): bool
    {
        return $this === $type;
    }
}
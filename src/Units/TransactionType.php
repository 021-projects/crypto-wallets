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

    public function is(TransactionType $type): bool
    {
        return $this === $type;
    }
}
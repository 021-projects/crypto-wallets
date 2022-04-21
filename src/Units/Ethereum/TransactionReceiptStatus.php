<?php

namespace O21\CryptoWallets\Units\Ethereum;

enum TransactionReceiptStatus: string
{
    case Success = '0x1';
    case Failure = '0x0';
}
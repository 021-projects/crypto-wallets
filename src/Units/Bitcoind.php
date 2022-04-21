<?php

namespace O21\CryptoWallets\Units;

enum Bitcoind: int
{
    case KByte = 1;
    case Byte = 1000;
    case Satoshi = 100_000_000;
}
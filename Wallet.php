<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Contracts\CryptoWallet;
use Illuminate\Support\Collection;

abstract class Wallet implements CryptoWallet
{
    /**
     * @param  array  $transactions
     * @return \Illuminate\Support\Collection|\O21\CryptoWallets\Transaction[]
     */
    protected function collectTransactionsFromArray(array $transactions): Collection
    {
        return collect(array_map(
            static fn($transaction) => new Transaction($transaction),
            $transactions
        ));
    }
}

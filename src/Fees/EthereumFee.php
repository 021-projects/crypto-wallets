<?php

namespace O21\CryptoWallets\Fees;

use O21\CryptoWallets\Units\Ethereum;

class EthereumFee extends AbstractFee
{
    public function toArray()
    {
        return [
            'value' => [
                'wei'   => $this->getValue(),
                'gwei'  => $this->getValue(Ethereum::Gwei),
                'ether' => $this->getValue(Ethereum::Ether)
            ],
            'approximate_time' => $this->getApproximateConfirmationTimeInAllUnits()
        ];
    }

    public function getValue(Ethereum $unit = Ethereum::Wei): string
    {
        return $unit->fromWei($this->value);
    }
}
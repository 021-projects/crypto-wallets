<?php

use O21\CryptoWallets\Units\Ethereum;

if (! function_exists('from_wei_to_eth')) {
    function from_wei_to_eth($number): string
    {
        return Ethereum::Ether->fromWei($number);
    }
}
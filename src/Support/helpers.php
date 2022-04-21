<?php

use Web3\Utils;

if (! function_exists('from_wei_to_eth')) {
    function from_wei_to_eth($number): string
    {
        [$bnq, $bnr] = Utils::fromWei($number, 'ether');

        $remainder = substr(
            // https://github.com/web3p/web3.php/issues/241#issuecomment-1010806449
            str_pad($bnr->toString(), 18, '0', STR_PAD_LEFT),
            0,
            8
        );

        return $bnq.'.'.$remainder;
    }
}
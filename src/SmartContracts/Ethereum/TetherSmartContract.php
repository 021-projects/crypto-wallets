<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum;

class TetherSmartContract extends AbstractERC20SmartContract
{
    public static function getAbi(): array
    {
        return json_decode(
            file_get_contents(__DIR__ . '/compiled/tether.abi'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public static function getByteCode(): string
    {
        return file_get_contents(__DIR__ . '/compiled/tether.bin');
    }
}
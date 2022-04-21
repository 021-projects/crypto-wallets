<?php

namespace O21\CryptoWallets\Units;

enum Ethereum: int
{
    case Wei = 1;
    case Kwei = 1000;
    case Mwei = 1_000_000;
    case Gwei = 1_000_000_000;
    case Ether = 1_000_000_000_000_000_000;

    public function toWei(float|int|string $value, bool $format = true): float|int|string
    {
        $result = bcmul($value, (string)$this->value, 8);
        return $format ? number_format_trim_trailing_zero($result, 8, '.', '') : $result;
    }

    public function fromWei(float|int|string $value, bool $format = true): float|int|string
    {
        $result = bcdiv($value, (string)$this->value, 8);
        return $format ? number_format_trim_trailing_zero($result, 8, '.', '') : $result;
    }
}
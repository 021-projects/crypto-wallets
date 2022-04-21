<?php

namespace O21\CryptoWallets\Interfaces;

use Illuminate\Contracts\Support\Arrayable;
use O21\CryptoWallets\Units\Time;

interface FeeInterface extends Arrayable
{
    /**
     *
     * @param  string|float  $value fee value
     * @param  int  $approximateConfirmationTime approximate transaction confirmation time in seconds
     */
    public function __construct(
        string|float $value,
        int $approximateConfirmationTime
    );

    public function getValue(): string;

    public function getConfirmationApproximateTime(Time $unit = Time::Second): int;
}
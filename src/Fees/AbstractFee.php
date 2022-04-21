<?php

namespace O21\CryptoWallets\Fees;

use O21\CryptoWallets\Interfaces\FeeInterface;
use O21\CryptoWallets\Units\Time;

abstract class AbstractFee implements FeeInterface
{
    public function __construct(
        protected float|string $value,
        protected int $approximateConfirmationTime
    ) {}

    public function getConfirmationApproximateTime(Time $unit = Time::Second): int
    {
        return round(match ($unit) {
            Time::Second => $this->approximateConfirmationTime,
            Time::Minute => $this->approximateConfirmationTime / 60,
            Time::Hour => $this->approximateConfirmationTime / 60 / 60
        });
    }

    protected function getApproximateConfirmationTimeInAllUnits(): array
    {
        return [
            'seconds' => $this->getConfirmationApproximateTime(),
            'minutes' => $this->getConfirmationApproximateTime(Time::Minute),
            'hours' => $this->getConfirmationApproximateTime(Time::Hour)
        ];
    }
}
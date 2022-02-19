<?php

namespace O21\CryptoWallets\Estimates;

use Illuminate\Contracts\Support\Arrayable;

class Fee implements Arrayable
{
    protected string $valuePerKb;

    protected int $blocks;

    protected int $approximateTimeInMinutes;

    /**
     *
     * @param  string|float  $valuePerKb
     * @param  int  $blocks
     * @param  int|null  $approximateTimeInMinutes
     */
    public function __construct(
        string|float $valuePerKb,
        int $blocks,
        ?int $approximateTimeInMinutes = null
    ) {
        $this->valuePerKb = crypto_number($valuePerKb);
        $this->blocks = $blocks;
        $this->approximateTimeInMinutes = $approximateTimeInMinutes ?: $blocks * 10;
    }

    public function toArray(): array
    {
        return [
            'blocks' => $this->blocks,
            'value_per_kbyte' => $this->valuePerKb,
            'value_per_byte' => $this->getValuePerByte(),
            'value_per_byte_in_satoshi' => $this->getValuePerByte(),
            'approximate_time_in_minutes' => $this->approximateTimeInMinutes,
        ];
    }

    public function getValuePerKb(): string
    {
        return $this->valuePerKb;
    }

    public function getValuePerByte(): string
    {
        return bcdiv($this->valuePerKb, 1000, 8);
    }

    public function getValuePerByteInSatoshi(): string
    {
        return bcmul($this->getValuePerByte(), '100000000', 8);
    }

    /**
     * @return int
     */
    public function getApproximateTimeInMinutes(): int
    {
        return $this->approximateTimeInMinutes;
    }

    /**
     * @return int
     */
    public function getBlocks(): int
    {
        return $this->blocks;
    }
}

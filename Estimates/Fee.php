<?php

namespace O21\CryptoWallets\Estimates;

class Fee
{
    protected float $valuePerKb;

    protected int $blocks;

    protected int $approximateTimeInMinutes;

    /**
     *
     * @param  float  $valuePerKb
     * @param  int  $blocks
     * @param  int|null  $approximateTimeInMinutes
     */
    public function __construct(
        float $valuePerKb,
        int $blocks,
        ?int $approximateTimeInMinutes = null
    ) {
        $this->valuePerKb = $valuePerKb;
        $this->blocks = $blocks;
        $this->approximateTimeInMinutes = $approximateTimeInMinutes ?: $blocks * 10;
    }

    /**
     * @return float
     */
    public function getValuePerKb(): float
    {
        return $this->valuePerKb;
    }

    /**
     * @return float
     */
    public function getValuePerByte(): float
    {
        return ($this->valuePerKb / 1000);
    }

    /**
     * @return float
     */
    public function getValuePerByteInSatoshi(): float
    {
        return $this->getValuePerByte() * 100000000;
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

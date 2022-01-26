<?php

namespace O21\CryptoWallets\Estimates;

class Fee
{
    protected float $valuePerKb;

    protected int $blocks;

    protected int $approximateTimeInSeconds;

    /**
     *
     * @param  float  $valuePerKb
     * @param  int  $blocks
     * @param  int|null  $approximateTimeInSeconds
     */
    public function __construct(
        float $valuePerKb,
        int $blocks,
        ?int $approximateTimeInSeconds = null
    ) {
        $this->valuePerKb = $valuePerKb;
        $this->blocks = $blocks;
        $this->approximateTimeInSeconds = $approximateTimeInSeconds ?: $blocks * 10;
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
        return ($this->valuePerKb / 1000) * 100000000;
    }

    /**
     * @return int
     */
    public function getApproximateTimeInSeconds(): int
    {
        return $this->approximateTimeInSeconds;
    }

    /**
     * @return int
     */
    public function getBlocks(): int
    {
        return $this->blocks;
    }
}

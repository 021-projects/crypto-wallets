<?php

namespace O21\CryptoWallets\Fees;

use O21\CryptoWallets\Units\Bitcoind;

class BitcoindFee extends AbstractFee
{
    public function __construct(
        protected float|string $value,
        protected int $approximateConfirmationTime,
        protected int $blocks = 10
    ) {
        $this->value = crypto_number($value);
    }

    public function toArray(): array
    {
        return [
            'blocks'                    => $this->blocks,
            'value_per_kbyte'           => $this->getValue(),
            'value_per_byte'            => $this->getValue(Bitcoind::Byte),
            'value_per_byte_in_satoshi' => $this->getValue(Bitcoind::Satoshi),
            'approximate_time'          => $this->getApproximateConfirmationTimeInAllUnits()
        ];
    }

    public function getValue(Bitcoind $unit = Bitcoind::KByte): string
    {
        return crypto_number(match ($unit) {
            Bitcoind::KByte   => $this->value,
            Bitcoind::Byte    => bcdiv($this->value, $unit->value, 11),
            Bitcoind::Satoshi => bcmul($this->getValue(Bitcoind::Byte), $unit->value, 11)
        });
    }

    /**
     * @return int
     */
    public function getBlocks(): int
    {
        return $this->blocks;
    }
}
<?php

namespace O21\CryptoWallets\Exceptions;

use Exception;

class AddressOutputException extends Exception
{
    public static function invalidAmount(): static
    {
        return new static('Invalid amount passed to address outputs.');
    }
}
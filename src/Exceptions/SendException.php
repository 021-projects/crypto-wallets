<?php

namespace O21\CryptoWallets\Exceptions;

class SendException extends \Exception
{
    public static function withError(string $message): static
    {
        return new static("Error while sending transaction: $message");
    }
}
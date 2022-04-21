<?php

namespace O21\CryptoWallets\Exceptions\Ethereum;

use Exception;

class EmptyPassphraseException extends Exception
{
    public function __construct(
        string $message = 'Empty passphrase. This function require passphrase.',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
<?php

namespace O21\CryptoWallets\Rates\Concerns;

use GuzzleHttp\Client;

trait HasApi
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }
}

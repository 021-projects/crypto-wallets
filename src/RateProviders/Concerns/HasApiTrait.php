<?php

namespace O21\CryptoWallets\RateProviders\Concerns;

use GuzzleHttp\Client;

trait HasApiTrait
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }
}

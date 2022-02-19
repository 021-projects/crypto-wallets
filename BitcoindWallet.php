<?php

namespace O21\CryptoWallets;

use O21\CryptoWallets\Contracts\WalletRate;
use Denpa\Bitcoin\Client;
use Illuminate\Support\Collection;
use O21\CryptoWallets\Estimates\Fee;
use O21\CryptoWallets\Exceptions\AddressOutputException;

abstract class BitcoindWallet extends Wallet
{
    protected Client $client;

    /**
     * @param  array  $config Denpa client config for connect
     * @param  string  $walletName Wallet name (empty if using default wallet)
     */
    public function __construct(
        array $config,
        string $walletName = ''
    ) {
        $this->client = (new Client($config))
            ->wallet($walletName);
    }

    /**
     * Number of blocks to confirm transaction
     *
     * @var int[]
     */
    protected array $confirmationBlocks = [
        2, 4, 6, 12, 24, 48, 144, 504
    ];

    abstract protected function getWalletRate(?string $source): WalletRate;

    /**
     * @return \Illuminate\Support\Collection<Fee>
     */
    abstract protected function getDefaultFees(): Collection;
    
    public function isAvailable(): bool
    {
        try {
            $this->client->getWalletInfo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getBalance(): string
    {
        return $this->client
            ->getBalance()
            ->result();
    }

    public function getTransactionsCount(): int
    {
        return $this->client->getWalletInfo()['txcount'];
    }

    public function getRate(string $currency = 'USD', ?string $source = null): float
    {
        return crypto_number(
            $this->getWalletRate($source)
                ->getRate($currency, $this->getSymbol())
        );
    }

    public function getBestRate(
        string $currency = 'USD',
        int $limit = null,
        int $interval = WalletRate::INTERVAL_MINUTES
    ): float {
        if (null === $limit) {
            $limit = $this->getDefaultBestRateLimit();
        }

        $rate = $this->getWalletRate();

        if ($rate->isSupportsHistory()) {
            return max($rate->history($currency, $this->getSymbol(), $limit, $interval));
        }

        return $this->getRate($currency);
    }

    public function getNewAddress(): string
    {
        return $this->client->getNewAddress()->result();
    }

    public function validateAddress(string $address): bool
    {
        return (bool)$this->client->validateAddress($address)->toArray()['isvalid'];
    }

    public function calcAmountIncludingFee(
        array|string $addresses,
        string $feeRatePerKb,
        ?string $amount,
        ?string &$error = null
    ): float {
        $transaction = $this->createAndFundTransaction($addresses, $feeRatePerKb, $amount, $error);

        if (! empty($transaction)) {
            return bcadd($amount, $transaction['fee'], 8);
        }

        return $amount;
    }

    public function sendToAddress(
        array|string $addresses,
        string $feeRatePerKb,
        ?string $amount,
        ?string &$error = null
    ): bool|string {
        $client = $this->client;

        $transaction = $this->createAndFundTransaction($addresses, $feeRatePerKb, $amount, $error);

        if (! empty($transaction)) {
            $rawTransaction = $transaction['hex'];

            $signedRawTransaction = $client->signRawTransactionWithWallet($rawTransaction)->result()['hex'];

            return $client->sendRawTransaction($signedRawTransaction)->result();
        }

        return false;
    }

    public function createAndFundTransaction(
        array|string $addresses,
        string $feeRatePerKb,
        ?string $amount,
        ?string &$error = null
    ): array {
        $client = $this->client;

        $transaction = [];

        try {
            $rawTransaction = $client->createRawTransaction(
                [],
                $this->getOutputsForAddresses($addresses, $amount)
            )->result();

            $transaction = $client->fundRawTransaction($rawTransaction, [
                'feeRate' => $feeRatePerKb
            ])->result();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $transaction;
    }

    /**
     * @throws \O21\CryptoWallets\Exceptions\AddressOutputException
     */
    protected function getOutputsForAddresses(array|string $addresses, ?string $amount): array
    {
        $outputs = [];

        if (is_array($addresses)) {
            $outputs = array_map('crypto_number', $addresses);
        } else {
            if (! $amount) {
                throw AddressOutputException::invalidAmount();
            }

            $outputs[$addresses] = $amount;
        }

        return $outputs;
    }

    public function getTransaction(string $txid): Transaction
    {
        return new Transaction($this->client->getTransaction($txid)->toArray());
    }

    public function getTransactions(int $count = 50, int $skip = 0): Collection
    {
        return $this->collectTransactionsFromArray(
            $this->client->listTransactions('*', $count, $skip)->toArray()
        )->reverse();
    }

    public function getTransactionsSinceBlock(string $block = ''): Collection
    {
        $client = $this->client;

        $transactions = data_get($client->listSinceBlock($block)->toArray(), 'transactions', []);

        return $this->collectTransactionsFromArray($transactions);
    }

    public function getEstimateFees(): Collection
    {
        $fees = collect();

        foreach ($this->confirmationBlocks as $blocks) {
            $fees->add(new Fee(
                $this->estimateSmartFee($blocks),
                $blocks
            ));
        }

        if (! bccomp($this->sumFees($fees), 0, 8)) {
            $fees = $this->getDefaultFees();
        }

        return $fees;
    }

    protected function estimateSmartFee(int $blocks)
    {
        return data_get($this->client->estimateSmartFee($blocks)->result(), 'feerate', 0);
    }

    /**
     * @param  \Illuminate\Support\Collection<Fee>  $fees
     * @return string
     */
    protected function sumFees(Collection $fees): string
    {
        return $fees->reduce(function ($result, Fee $fee) {
            return bcadd($result, $fee->getValuePerKb(), 8);
        }, 0);
    }

    /**
     * @return \Denpa\Bitcoin\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}

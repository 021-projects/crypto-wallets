<?php

namespace O21\CryptoWallets;

use Denpa\Bitcoin\Client;
use Denpa\Bitcoin\Exceptions\BadRemoteCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use O21\CryptoWallets\Concerns\GetRatesTrait;
use O21\CryptoWallets\Fees\BitcoindFee;
use O21\CryptoWallets\Exceptions\Bitcoind\CreateRawTransactionException;
use O21\CryptoWallets\Exceptions\Bitcoind\FundRawTransactionException;
use O21\CryptoWallets\Interfaces\BitcoindWalletInterface;
use O21\CryptoWallets\Interfaces\ConnectConfigInterface;
use O21\CryptoWallets\Interfaces\FeeInterface;
use O21\CryptoWallets\Interfaces\WalletInterface;
use O21\CryptoWallets\Models\BitcoindTransaction;

abstract class AbstractBitcoindWallet extends AbstractWallet implements WalletInterface, BitcoindWalletInterface
{
    use GetRatesTrait;

    protected Client $client;

    /**
     * @param  \O21\CryptoWallets\Interfaces\ConnectConfigInterface  $config
     * @throws \Denpa\Bitcoin\Exceptions\BadConfigurationException
     */
    public function __construct(ConnectConfigInterface $config)
    {
        $this->client = (new Client($config->toArray()))
            ->wallet($config->getWalletName());
    }

    /**
     * Number of blocks to confirm transaction
     *
     * @var int[]
     */
    protected array $confirmationBlocks = [
        2, 4, 6, 12, 24, 48, 144, 504
    ];

    /**
     * @return \Illuminate\Support\Collection<BitcoindFee>
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
        return $this->client->getBalance()
            ->result();
    }

    public function getNewAddress($config = null): string
    {
        return $this->client->getNewAddress()->result();
    }

    public function isValidAddress(string $address): bool
    {
        return (bool)Arr::get(
            $this->client->validateAddress($address)->toArray(),
            'isvalid',
            false
        );
    }

    public function isOwningAddress(string $address): bool
    {
        try {
            return (bool)Arr::get(
                $this->client->getAddressInfo($address)->toArray(),
                'ismine',
                false
            );
        } catch (BadRemoteCallException $e) {
            if ($e->getMessage() === 'Invalid address') {
                return false;
            }

            throw $e;
        }
    }

    public function estimateSendingFee(
        string $to,
        string $value,
        FeeInterface|string $fee
    ): string {
        $transaction = $this->createAndFundTransaction($to, $value, $fee);
        return crypto_number($transaction['fee']);
    }

    public function send(string $to, string $value, FeeInterface|string $fee): string
    {
        $client = $this->client;

        $transaction = $this->createAndFundTransaction($to, $value, $fee);

        $rawTransaction = $transaction['hex'];
        $signedRawTransaction = $client->signRawTransactionWithWallet($rawTransaction)
            ->result()['hex'];

        return $client->sendRawTransaction($signedRawTransaction)->result();
    }

    /**
     * @param  string  $to
     * @param  string  $amount
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string $fee value per kbyte
     * @return array
     * @throws \Throwable
     */
    public function createAndFundTransaction(
        string $to,
        string $amount,
        FeeInterface|string $fee
    ): array {
        $client = $this->client;

        $rawTransaction = $this->createRawTransaction(outputs: [$to => $amount]);
        throw_if(empty($rawTransaction), CreateRawTransactionException::class);

        $rawTransaction = $client->fundRawTransaction($rawTransaction, [
            'feeRate' => $this->feeValue($fee)
        ])->result();
        throw_if(empty($rawTransaction), FundRawTransactionException::class);

        return $rawTransaction;
    }

    public function createRawTransaction(
        array $inputs = [],
        array $outputs = [],
        int $lockTime = 0,
        bool $replaceable = false
    ): string {
        return $this->client->createRawTransaction(
            $inputs,
            $outputs,
            $lockTime,
            $replaceable
        )->result();
    }

    public function fundRawTransaction(string $hexString, ?array $options = null): array
    {
        return $this->client->fundRawTransaction($hexString, $options)
            ->result();
    }

    public function getTransaction(string $hash): BitcoindTransaction
    {
        return BitcoindTransaction::fromRpcTransaction(
            $this->client->getTransaction($hash)->toArray()
        );
    }

    public function getTransactions(int $count = 50, int $skip = 0): Collection
    {
        return $this->collectTransactionsFromRpcList(
            $this->client->listTransactions('*', $count, $skip)->toArray()
        )->reverse();
    }

    public function getTransactionsSinceBlock(string $block): Collection
    {
        $client = $this->client;

        $transactions = data_get($client->listSinceBlock($block)->toArray(), 'transactions', []);

        return $this->collectTransactionsFromRpcList($transactions);
    }

    protected function collectTransactionsFromRpcList(array $transactions): Collection
    {
        return collect(array_map(
            static fn($tx) => BitcoindTransaction::fromRpcTransaction($tx),
            $transactions
        ));
    }

    public function getTransactionsCount(): int
    {
        return $this->client->getWalletInfo()['txcount'];
    }

    public function getNetworkFees(): Collection
    {
        $fees = collect();

        foreach ($this->confirmationBlocks as $blocks) {
            $fees->add(new BitcoindFee(
                $this->estimateSmartFee($blocks),
                $blocks * 60 * 10,
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
     * @param  \Illuminate\Support\Collection<BitcoindFee>  $fees
     * @return string
     */
    protected function sumFees(Collection $fees): string
    {
        return $fees->reduce(function ($result, BitcoindFee $fee) {
            return bcadd($result, $fee->getValue(), 8);
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

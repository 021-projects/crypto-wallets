<?php

namespace O21\CryptoWallets;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Concerns\EthereumCallsTrait;
use O21\CryptoWallets\Fees\EthereumFee;
use O21\CryptoWallets\Exceptions\Ethereum\CantGetNetworkStatsException;
use O21\CryptoWallets\Exceptions\Ethereum\EmptyPassphraseException;
use O21\CryptoWallets\Interfaces\ConnectConfigInterface;
use O21\CryptoWallets\Interfaces\EthereumWalletInterface;
use O21\CryptoWallets\Interfaces\FeeInterface;
use O21\CryptoWallets\Interfaces\RateProviderInterface as RateProvider;
use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\CryptoWallets\Interfaces\WalletInterface;
use O21\CryptoWallets\Models\EthereumBlock;
use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Models\EthereumTransaction;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\RateProviders\BinanceProvider;
use O21\CryptoWallets\Units\Ethereum;
use O21\EthereumOracles\Contracts\OracleApi;
use O21\EthereumOracles\OffChain\Blockscout;
use O21\EthereumOracles\OffChain\Etherchain;
use O21\EthereumOracles\OffChain\EthGasStation;
use O21\EthereumOracles\OffChain\MaticNetwork;
use Web3\Personal;
use Web3\Utils;
use Web3\Web3;

class EthereumWallet extends AbstractWallet implements WalletInterface, EthereumWalletInterface
{
    use EthereumCallsTrait;

    protected Web3 $web3;

    protected Personal $personal;

    protected string $coinbase;

    public function __construct(ConnectConfigInterface $config)
    {
        $this->web3 = new Web3($config->toUrl());
        $this->eth = $this->web3->getEth();
        $this->personal = $this->web3->getPersonal();
        $this->coinbase = $this->ethCall('coinbase');
    }

    public function isAvailable(): bool
    {
        $available = true;

        $this->web3->clientVersion(function ($err, $version) use (&$available) {
            $available = empty($err) && ! empty($version);
        });

        return $available;
    }

    public function getBalance(?string $address = null): string
    {
        $address ??= $this->coinbase;
        return from_wei_to_eth(
            $this->ethCall('getBalance', [$address])
        );
    }

    public function getNewAddress($passphrase = null): string
    {
        throw_if(empty($passphrase), EmptyPassphraseException::class);

        return $this->personalCall('newAccount', [$passphrase]);
    }

    public function isValidAddress(string $address): bool
    {
        return Utils::isAddress($address);
    }

    public function isOwningAddress(string $address): bool
    {
        $accounts = $this->ethCall('accounts');
        if (! is_array($accounts)) {
            $accounts = [];
        }

        return in_array($address, $accounts, true);
    }

    public function getExploreAddressLink(string $address): string
    {
        return "https://etherscan.io/address/$address";
    }

    /**
     * @param  string  $to
     * @param  string  $value  Transaction amount in Wei
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string  $fee  Tip for miners in Wei
     * @param  string|null  $from
     * @return string 21000 * base fee + fee value
     */
    public function estimateSendingFee(
        string $to,
        string $value,
        FeeInterface|string $fee,
        ?string $from = null
    ): string {
        $baseFee = $this->getBlock($this->getLastBlockNumber())->baseFeePerGas;
        return bcmul('21000', bcadd($this->feeValue($fee), bcmul('2', $baseFee)));
    }

    /**
     * @param  string  $to
     * @param  string  $value  Transaction amount in Wei
     * @param  \O21\CryptoWallets\Interfaces\FeeInterface|string  $fee  Tip for miners in Wei
     * @param  string|null  $from  From which account to send.
     * @return string
     */
    public function send(
        string $to,
        string $value,
        FeeInterface|string $fee,
        ?string $from = null
    ): string {
        $baseFee = $this->getBlock($this->getLastBlockNumber())->baseFeePerGas;
        $tip = $this->feeValue($fee);

        $call = new EthereumCall(
            from                : $from ?? $this->getCoinbase(),
            to                  : $to,
            maxPriorityFeePerGas: $tip,
            maxFeePerGas        : bcadd($tip, bcmul('2', $baseFee)),
            value               : $value
        );

        return $this->ethCall('sendTransaction', [$call->toArray()]);
    }

    public function getBlock(int|string $hashOrNumber, bool $fullTransactions = false): EthereumBlock
    {
        $methodName = is_string($hashOrNumber) ? 'getBlockByHash' : 'getBlockByNumber';

        return EthereumBlock::fromRpcBlock(
            $this->ethCall($methodName, [$hashOrNumber, $fullTransactions]),
            $this->getLastBlockNumber()
        );
    }

    public function getAccounts(): array
    {
        return $this->ethCall('accounts');
    }

    public function getLastBlockNumber(): int
    {
        return (int)(string)$this->ethCall('blockNumber');
    }

    public function getSmartContract(string $class): SmartContractInterface
    {
        return new $class($this);
    }

    public function getTransaction(string $hash): ?EthereumTransaction
    {
        $tx = $this->ethCall('getTransactionByHash', [$hash]);
        if (! $tx) {
            return null;
        }

        $block = $tx->blockNumber
            ? $this->ethCall('getBlockByNumber', [ $tx->blockNumber, false ])
            : null;

        return EthereumTransaction::fromRpcTransaction(
            $tx,
            $block,
            $this->getLastBlockNumber()
        );
    }

    public function getTransactionReceipt(
        string $hash,
        SmartContractInterface|string|null $contract = null
    ): ?EthereumTransactionReceipt {
        $receiptStd = $this->ethCall('getTransactionReceipt', [$hash]);
        if (empty($receiptStd)) {
            return null;
        }

        if (is_string($contract)) {
            $contract = $this->getSmartContract($contract);
        }

        return EthereumTransactionReceipt::fromRpcReceipt($receiptStd, $contract);
    }

    /**
     * @param  int  $count
     * @param  int  $skip
     * @return \Illuminate\Support\Collection<EthereumTransaction>
     */
    public function getTransactions(int $count = 50, int $skip = 0): Collection
    {
        $lastBlockNumber = $this->getLastBlockNumber();

        $transactions = collect();
        $skipped = 0;

        for ($i = $lastBlockNumber; $i > 0 && $transactions->count() < $count; $i = bcsub($i, '1')) {
            $blockTransactions = $this->getTransactionsInBlock($i, $lastBlockNumber);

            if ($skipped < $skip) {
                $needToSkip = $skip - $skipped;
                $skipped += min($needToSkip, $blockTransactions->count());
                $blockTransactions->skip($needToSkip);
            }

            $transactions->push(...$blockTransactions->all());
        }

        return $transactions->slice(0, $count);
    }

    public function getTransactionsCount(?string $address = null): int
    {
        $address ??= $this->coinbase;
        return (int)$this->ethCall('getTransactionCount', [$address])->toString();
    }

    /**
     * @param  string|int  $blockNumber
     * @param  string|int|null  $lastBlockNumber
     * @return \Illuminate\Support\Collection<EthereumTransaction>
     */
    public function getTransactionsInBlock(
        string|int $blockNumber,
        string|int|null $lastBlockNumber = null
    ): Collection {
        $block = $this->ethCall('getBlockByNumber', [(int)$blockNumber, true]);
        $lastBlockNumber ??= $this->getLastBlockNumber();

        return collect(
            array_map(
                static fn(\stdClass $tx)
                    => EthereumTransaction::fromRpcTransaction($tx, $block, (int)(string)$lastBlockNumber),
                $block->transactions
            )
        );
    }

    /**
     * @param  string  $block
     * @return \Illuminate\Support\Collection<EthereumTransaction>
     */
    public function getTransactionsSinceBlock(string $block): Collection
    {
        $lastBlockNumber = $this->getLastBlockNumber();

        $transactions = collect();

        for ($i = $block; bccomp($i, $lastBlockNumber) < 0; $i = bcadd($i, '1')) {
            $transactions->push(
                ...$this->getTransactionsInBlock($i, $lastBlockNumber)->all()
            );
        }

        return $transactions;
    }

    public function getExploreTransactionLink(string $hash): string
    {
        return "https://etherchain.org/tx/$hash";
    }

    /**
     * Returns recommended network tips for miners.
     *
     * @param  \O21\EthereumOracles\Contracts\OracleApi|null  $oracle
     * @return Collection<EthereumFee>
     * @throws \O21\CryptoWallets\Exceptions\Ethereum\CantGetNetworkStatsException
     */
    public function getNetworkFees(?OracleApi $oracle = null): Collection
    {
        if (! $oracle) {
            return $this->getNetworkFeesFromAnyOracle();
        }

        $stats = $oracle->getFeeStats();

        $fees = [];

        $fees[] = new EthereumFee(
            Ethereum::Gwei->toWei($stats->slow),
            30 * 60
        );
        $fees[] = new EthereumFee(
            Ethereum::Gwei->toWei($stats->standard),
            5 * 60
        );
        $fees[] = new EthereumFee(
            Ethereum::Gwei->toWei($stats->high),
            2 * 60
        );

        if ($stats->instant) {
            $fees[] = new EthereumFee(
                Ethereum::Gwei->toWei($stats->instant),
                30
            );
        }

        return collect($fees);
    }

    public function getNetworkFeesFromAnyOracle(
        ?OracleApi $preferredOracle = null
    ): Collection {
        $oracles = array_unique([
            EthGasStation::class,
            Blockscout::class,
            Etherchain::class,
            MaticNetwork::class
        ]);
        if ($preferredOracle) {
            array_unshift($oracles, get_class($preferredOracle));
        }

        foreach ($oracles as $oracleClass) {
            try {
                return $this->getNetworkFees((new $oracleClass));
            } catch (\Exception $e) {}
        }

        throw new CantGetNetworkStatsException;
    }

    public function getDefaultBestRateLimit(): int
    {
        return 60;
    }

    public function getTypicalTransactionSize(): int
    {
        return 3000;
    }

    public function getSymbol(): string
    {
        return 'ETH';
    }

    /**
     * @return string
     */
    public function getCoinbase(): string
    {
        return $this->coinbase;
    }

    public function personalCall(string $method, array $params = [], bool $single = true): mixed
    {
        return $this->namespaceCall($this->personal, $method, $params, $single);
    }

    protected function getRateProvider(?RateProvider $provider = null): RateProvider
    {
        return $provider ?? new BinanceProvider;
    }
}
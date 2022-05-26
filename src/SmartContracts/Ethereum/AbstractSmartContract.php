<?php

namespace O21\CryptoWallets\SmartContracts\Ethereum;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Concerns\EthereumCallsTrait;
use O21\CryptoWallets\Exceptions\Ethereum\SmartContractAlreadyDeployedException;
use O21\CryptoWallets\Exceptions\Ethereum\SmartContractNotDeployedException;
use O21\CryptoWallets\Interfaces\EthereumWalletInterface;
use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Models\EthereumTransactionLog;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\SmartContracts\Ethereum\Filters\LogsFilter;
use O21\CryptoWallets\Web3\EthAbi;
use Web3\Contract;

abstract class AbstractSmartContract implements SmartContractInterface
{
    use EthereumCallsTrait;

    protected Contract $contract;

    protected EthAbi $abi;

    protected ?string $address = null;

    public function __construct(
        protected EthereumWalletInterface $wallet,
        ?string $address = null
    ) {
        $this->eth = $wallet->getEth();
        $this->contract = (new Contract($this->eth->getProvider(), static::getAbi()))
            ->bytecode(static::getByteCode());
        $this->abi = new EthAbi($this->contract);

        $this->setAddress($address);
    }

    public function deploy(
        DeployParams $params,
        string &$error = null
    ): ?EthereumTransactionReceipt {
        $this->assertNotDeployed();

        $receipt = null;

        $deployParams = $params->toArray();
        $deployParams[] = function ($err, $hash) use (&$receipt, &$error) {
            if ($err && ! $hash) {
                $error = $err->getMessage();
                return;
            }

            $receipt = $this->wallet->getTransactionReceipt($hash);
            if (! $receipt) {
                $error = "Can't get transaction receipt for created contract.";
                return;
            }

            $this->setAddress($receipt->contractAddress);
        };

        $this->contract->new(...$deployParams);

        return $receipt;
    }

    public function decodeData(string $data, array $topics): array
    {
        return $this->abi->decodeEventLogData($data, $topics);
    }

    public function getLogs(?LogsFilter $filter = null): Collection
    {
        $this->assertDeployed();

        $filter ??= new LogsFilter();
        $filter->address = $this->address;

        return collect(
            array_map(
                fn(\stdClass $log) => EthereumTransactionLog::fromRpcLog($log, $this),
                $this->ethCall('getLogs', [$filter->toArray()])
            )
        );
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param  string|null  $address
     * @return SmartContractInterface
     */
    public function setAddress(?string $address): SmartContractInterface
    {
        $this->address = $address;

        if ($address) {
            $this->contract->at($address);
        }

        return $this;
    }

    public function call(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        bool $single = true
    ): mixed {
        $result = [];

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $call,
            function ($err, ...$args) use (&$result) {
                if ($err !== null) {
                    throw $err;
                }

                $result = $args;
            }
        );
        $this->contract->call(...$arguments);

        return $single ? first($result) : $result;
    }

    public function send(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        ?string &$error = null
    ): ?string {
        $this->assertDeployed();

        $hash = null;

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $this->assertEthereumCallFromDefined($call),
            function ($err, $txid) use (&$error, &$hash) {
                $error = $err;
                $hash = $txid;
            }
        );
        $this->contract->send(...$arguments);

        return $hash;
    }

    public function estimateGas(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        ?string &$error = null
    ): ?string {
        $this->assertDeployed();

        $gas = null;

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $this->assertEthereumCallFromDefined($call),
            function ($err, $_gas) use (&$error, &$gas) {
                $error = $err;
                $gas = $_gas;
            }
        );
        $this->contract->estimateGas(...$arguments);

        return $gas;
    }

    protected function buildContractMethodArguments(
        string $method,
        array $params,
        ?EthereumCall $call = null,
        ?callable $callback = null
    ): array {
        if (! $call) {
            $call = new EthereumCall();
        }

        $callback ??= static function ($err) {
            if ($err !== null) {
                throw $err;
            }
        };

        return [
            $method,
            ...$params,
            $call->toArray(),
            $callback
        ];
    }

    protected function getCoinbase(): string
    {
        return $this->ethCall('coinbase');
    }

    protected function assertEthereumCallFromDefined(?EthereumCall $call = null): EthereumCall
    {
        if (! $call) {
            $call = new EthereumCall($this->getCoinbase());
        }

        if (! $call->from) {
            $call->from = $this->getCoinbase();
        }

        return $call;
    }

    protected function assertDeployed(): void
    {
        throw_unless($this->address, SmartContractNotDeployedException::class);
    }

    protected function assertNotDeployed(): void
    {
        throw_if($this->address, SmartContractAlreadyDeployedException::class, $this->address);
    }
}
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
use O21\CryptoWallets\Support\Error;

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

    public function deploy(DeployParams $params): ?EthereumTransactionReceipt
    {
        $this->assertNotDeployed();

        $receipt = null;

        $deployParams = $params->toArray();
        $deployParams[] = function ($err, $hash) use (&$receipt) {
            Error::assertEmpty($err);

            $receipt = $this->wallet->getTransactionReceipt($hash);
            if (! $receipt) {
                throw SmartContractNotDeployedException::noReceipt();
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
        if ($address) {
            $address = mb_strtolower($address);
            $this->contract->at($address);
        }

        $this->address = $address;

        return $this;
    }

    public function call(
        string $method,
        array $params = [],
        ?EthereumCall $call = null,
        bool $single = true
    ): mixed {
        $result = [];

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $call,
            function ($err, ...$args) use (&$result) {
                Error::assertEmpty($err);

                $result = $args;
            }
        );
        $this->contract->call(...$arguments);

        return $single ? first($result) : $result;
    }

    public function send(
        string $method,
        array $params = [],
        ?EthereumCall $call = null
    ): ?string {
        $this->assertDeployed();

        $hash = null;

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $this->assertEthereumCallFromDefined($call),
            function ($err, $txid) use (&$hash) {
                Error::assertEmpty($err);

                $hash = $txid;
            }
        );
        $this->contract->send(...$arguments);

        return $hash;
    }

    public function estimateGas(
        string $method,
        array $params = [],
        ?EthereumCall $call = null
    ): ?string {
        $this->assertDeployed();

        $gas = null;

        $arguments = $this->buildContractMethodArguments(
            $method,
            $params,
            $this->assertEthereumCallFromDefined($call),
            function ($err, $_gas) use (&$gas) {
                Error::assertEmpty($err);
                $gas = $_gas;
            }
        );
        $this->contract->estimateGas(...$arguments);

        return $gas;
    }

    protected function buildContractMethodArguments(
        string $method,
        array $params = [],
        ?EthereumCall $call = null,
        ?callable $callback = null
    ): array {
        if (! $call) {
            $call = new EthereumCall();
        }

        $callback ??= static function ($err) {
            Error::assertEmpty($err);
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
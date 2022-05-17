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
     * @return AbstractSmartContract
     */
    public function setAddress(?string $address): AbstractSmartContract
    {
        $this->address = $address;

        if ($address) {
            $this->contract->at($address);
        }

        return $this;
    }

    public function call(string $method, bool $single = true, ...$params): mixed
    {
        $result = [];

        $params[] = static function ($err, ...$args) use (&$result) {
            if ($err !== null) {
                throw $err;
            }

            $result = $args;
        };

        $this->contract->call($method, ...$params);

        return $single ? first($result) : $result;
    }

    public function sendContractMethod(
        string $method,
        array $params,
        ?string $from = null,
        ?string &$error = null
    ): ?string {
        $this->assertDeployed();

        $hash = null;
        $from ??= $this->ethCall('coinbase');

        $arguments = [
            $method,
            ...$params,
            (new EthereumCall($from))->toArray(),
            function ($err, $txid) use (&$error, &$hash) {
                $error = $err;
                $hash = $txid;
            }
        ];
        $this->contract->send(...$arguments);

        return $hash;
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
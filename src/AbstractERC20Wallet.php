<?php

namespace O21\CryptoWallets;

use Illuminate\Support\Collection;
use O21\CryptoWallets\Interfaces\ConnectConfigInterface;
use O21\CryptoWallets\Interfaces\ERC20\WalletInterface as IERC20Wallet;
use O21\CryptoWallets\Interfaces\ERC20\TokenContractInterface as ITokenContract;
use O21\CryptoWallets\Interfaces\ERC20\ConnectConfigInterface as IConnectConfig;
use O21\CryptoWallets\Interfaces\FeeInterface;
use O21\CryptoWallets\Interfaces\SmartContractInterface;
use O21\CryptoWallets\Models\ERC20EthereumTransaction;
use O21\CryptoWallets\Models\ERC20EthereumTransactionReceipt;
use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Support\Number;

abstract class AbstractERC20Wallet extends EthereumWallet implements IERC20Wallet
{
    protected ITokenContract $contract;

    protected int $decimals; // token decimals

    /**
     * Create token smart contract instance.
     *
     * @param  string  $address
     * @return \O21\CryptoWallets\Interfaces\ERC20\TokenContractInterface
     */
    abstract protected function _createSmartContract(string $address): ITokenContract;

    public function __construct(ConnectConfigInterface|IConnectConfig $config)
    {
        parent::__construct($config);
        $this->contract = $this->_createSmartContract($config->getContractAddress());
        $this->decimals = $config->getTokenDecimals();
    }

    public function approveToSend(string $spender, string|int $amount): string
    {
        if ($amount > 0 && $this->getAllowance($spender) > 0) {
            $this->contract->approve($spender, 0);
        }

        return $this->contract->approve($spender, $amount);
    }

    public function getBalance(?string $address = null): string
    {
        return $this->fromWeiToToken(
            $this->contract->balanceOf($address ?? $this->coinbase)
        );
    }

    public function getAllowance(string $spender, ?string $owner = null): string
    {
        return $this->fromWeiToToken(
            $this->contract->allowance($owner ?? $this->coinbase, $spender)
        );
    }

    public function estimateSendingFee(
        string $to,
        string $value,
        string|FeeInterface $fee,
        ?string $from = null
    ): string {
        $call = $this->getFeeEthCall($fee);
        if ($from) {
            return $this->contract->estimateTransferFromGas(
                $from,
                $to,
                $value,
                $call
            );
        }
        return $this->contract->estimateTransferGas($to, $value, $call);
    }

    public function send(
        string $to,
        string $value,
        string|FeeInterface $fee,
        ?string $from = null
    ): string {
        $call = $this->getFeeEthCall($fee);
        if ($from) {
            return $this->contract->transferFrom(
                $from,
                $to,
                $value,
                $call
            );
        }
        return $this->contract->transfer($to, $value, $call);
    }

    protected function getFeeEthCall(string|FeeInterface $fee): EthereumCall
    {
        $baseFee = $this->getBlock($this->getLastBlockNumber())->baseFeePerGas;
        $tip = $this->feeValue($fee);

        return new EthereumCall(
            maxPriorityFeePerGas: $tip,
            maxFeePerGas        : bcadd($tip, bcmul('2', $baseFee))
        );
    }

    public function getTransactionsCount(?string $address = null): int
    {
        return parent::getTransactionsCount(
            $address ?? $this->getContractAddress()
        );
    }

    public function getTransaction(string $hash): ?ERC20EthereumTransaction
    {
        $tx = $this->ethCall('getTransactionByHash', [$hash]);
        if (! $tx) {
            return null;
        }

        $block = $tx->blockNumber
            ? $this->ethCall('getBlockByNumber', [$tx->blockNumber, false])
            : null;

        /** @var ERC20EthereumTransactionReceipt $receipt */
        $receipt = $this->getTransactionReceipt($hash);

        return $this->wrapEthereumTransaction(
            $tx,
            $block,
            $this->getLastBlockNumber(),
            $receipt
        );
    }

    /**
     * @param  int|string  $blockNumber
     * @param  int|string|null  $lastBlockNumber
     * @return \Illuminate\Support\Collection<ERC20EthereumTransaction>
     */
    public function getTransactionsInBlock(
        int|string $blockNumber,
        int|string|null $lastBlockNumber = null
    ): Collection {
        $block = $this->ethCall('getBlockByNumber', [(int)$blockNumber, true]);
        $lastBlockNumber ??= $this->getLastBlockNumber();

        $contractAddress = $this->getContractAddress();
        $contractTransactions = array_filter(
            $block->transactions,
            static fn (\stdClass $tx) => $tx->to === $contractAddress
        );

        return collect(
            array_map(
                function (\stdClass $tx) use ($block, $lastBlockNumber) {
                    /** @var ERC20EthereumTransactionReceipt|null $receipt */
                    $receipt = $this->getTransactionReceipt($tx->hash);
                    return $this->wrapEthereumTransaction(
                        $tx,
                        $block,
                        (int)(string)$lastBlockNumber,
                        $receipt
                    );
                },
                $contractTransactions
            )
        );
    }

    protected function wrapEthereumTransaction(
        \stdClass $tx,
        ?\stdClass $block,
        int $lastBlockNumber,
        ?ERC20EthereumTransactionReceipt $receipt = null
    ): ERC20EthereumTransaction {
        return $receipt
            ? ERC20EthereumTransaction::fromRpcTransactionWithReceipt(
                $tx,
                $block,
                $this->getLastBlockNumber(),
                $receipt
            )
            : ERC20EthereumTransaction::fromRpcTransaction(
                $tx,
                $block,
                $this->getLastBlockNumber()
            );
    }

    protected function wrapEthereumTransactionReceipt(
        \stdClass $receipt,
        ?SmartContractInterface $contract = null
    ): ERC20EthereumTransactionReceipt {
        return ERC20EthereumTransactionReceipt::fromRpcReceipt(
            $receipt,
            $contract ?? $this->contract
        );
    }

    public function fromWeiToToken($value): string
    {
        return Number::trimRightZero(
            bcdiv(
                (string)$value,
                str_pad('1', $this->decimals + 1, '0', STR_PAD_RIGHT),
                $this->decimals
            )
        );
    }

    public function fromTokenToWei($value): string
    {
        return Number::trimRightZero(
            bcmul(
                (string)$value,
                str_pad('1', $this->decimals + 1, '0', STR_PAD_RIGHT),
                $this->decimals
            )
        );
    }

    /**
     * @return \O21\CryptoWallets\Interfaces\ERC20\TokenContractInterface
     */
    public function getContract(): ITokenContract
    {
        return $this->contract;
    }

    public function getContractAddress(): string
    {
        return $this->contract->getAddress();
    }
}
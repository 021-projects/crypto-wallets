<?php

namespace Tests;

use O21\CryptoWallets\Configs\ERC20ConnectConfig;
use O21\CryptoWallets\Fees\EthereumFee;
use O21\CryptoWallets\Support\Number;
use O21\CryptoWallets\TetherWallet;
use O21\CryptoWallets\Units\Ethereum;

class TetherWalletTest extends TestCase
{
    protected TetherWallet $wallet;

    protected EthereumFee $feeTest;

    protected string $testAccount;

    public const SEND_AMOUNT = '666.666';

    protected function setUp(): void
    {
        parent::setUp();

        $this->wallet = new TetherWallet(
            new ERC20ConnectConfig(
                $_ENV['ETH_USER'],
                $_ENV['ETH_PASSWORD'],
                $_ENV['ETH_HOST'],
                $_ENV['ETH_PORT'],
                contractAddress: $_ENV['TETHER_CONTRACT_ADDRESS']
            )
        );
        $this->testAccount = $this->wallet->getAccounts()[1] ?? '';
        if (! $this->testAccount) {
            $this->markTestIncomplete("Can't test, because server has only one account.");
        }
        $this->feeTest = new EthereumFee(Ethereum::Gwei->toWei(21), 60);
    }

    public function testGetBalance(): void
    {
        $this->assertIsString($this->wallet->getBalance());
    }

    public function testApproveToSend(): void
    {
        $this->assertIsString(
            $this->wallet->approveToSend(
                $this->testAccount,
                $this->wallet->fromTokenToWei(self::SEND_AMOUNT)
            )
        );

        $this->assertGreaterThan(
            0,
            $this->wallet->getAllowance($this->testAccount)
        );
    }

    /**
     * @depends testApproveToSend
     * @return void
     */
    public function testSending(): void
    {
        $client = $this->wallet;

        $sendAmountInWei = $client->fromTokenToWei(self::SEND_AMOUNT);

        $hash = $client->send(
            $this->testAccount,
            $sendAmountInWei,
            $this->feeTest
        );

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);

        $transaction = $client->getTransaction($hash);

        $this->assertEquals($this->testAccount, $transaction->erc20Info?->to);
        $this->assertEquals($client->getCoinbase(), $transaction->erc20Info?->from);
        $this->assertEquals($sendAmountInWei, $transaction->erc20Info?->value);
    }

    public function testEstimateSendingFee(): void
    {
        $estimatedFee = $this->wallet->estimateSendingFee(
            '',
            self::SEND_AMOUNT,
            $this->feeTest
        );

        $this->assertGreaterThan(52000, $estimatedFee);
    }

    public function testGetRates(): void
    {
        $this->assertIsFloat($this->wallet->getRate());
        $this->assertIsFloat($this->wallet->getBestRate());
    }

    public function testNumberTrimRightZero(): void
    {
        $this->assertEquals('0', Number::trimRightZero('0.0000'));
        $this->assertEquals('0.21', Number::trimRightZero('0.2100'));
        $this->assertEquals('0.0021', Number::trimRightZero('0.002100'));
        $this->assertEquals('250', Number::trimRightZero('250.00'));
    }

    public function testUnitCalculation(): void
    {
        $this->assertEquals(self::SEND_AMOUNT, $this->wallet->fromWeiToToken('666666000'));
    }

    public function testGetTransactions(): void
    {
        $transactions = $this->wallet->getTransactions();
        $this->assertNotEmpty($transactions);
    }

    public function testGetTransactionsSinceBlock(): void
    {
        $transactions = $this->wallet
            ->getTransactionsSinceBlock(1);

        $this->assertNotEmpty($transactions);
        $this->assertIsString($transactions->first()?->hash);
    }

    public function testGetTransactionReceipt(): void
    {
        $this->markTestSkipped('Not implemented yet');
        // dd($this->wallet->getTransaction('0x1a2f3f7616b8cbb3261b38bb57f07eaa5e32a829e95e21ded138631cc4fffd07'));
    }

    public function testGetInvalidTransaction(): void
    {
        $tx = $this->wallet->getTransaction('0x98749d64c420d16cefa0054e1ae89a5450a49a71cf98f7acfcf0b624030558a3');
        $this->assertNull($tx);
    }

    public function testGetInvalidTransactionReceipt(): void
    {
        $tx = $this->wallet->getTransactionReceipt('0x98749d64c420d16cefa0054e1ae89a5450a49a71cf98f7acfcf0b624030558a3');
        $this->assertNull($tx);
    }
}
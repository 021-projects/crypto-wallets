<?php

namespace Tests;

use Denpa\Bitcoin\Client as DenpaClient;
use O21\CryptoWallets\BitcoinWallet;
use O21\CryptoWallets\Configs\ConnectConfig;
use O21\CryptoWallets\Fees\BitcoindFee;
use O21\CryptoWallets\Models\BitcoindTransaction;
use O21\CryptoWallets\Units\TransactionType;

class BitcoindWalletTest extends TestCase
{
    private const DESTINATION_ADDRESS = 'bcrt1q0t7kyuarqnhrpvnpz0jzx6lefwy80vlak36zya';

    private const INVALID_ADDRESS = 'bcrt1q0t7kyuarqnhrpvnpz0xxx6lefwy80vlak36zya';

    private const SEND_AMOUNT = '0.00210021';

    private const RAW_TX = '02000000000165340300000000001600147afd6273a304ee30b26113e4236bf94b8877b3fd00000000';

    private const BLOCKHASH = '2c06b0b3c124a651a03c4507c4dc35b9565c21e604c7ac31dfe3d87c8a76e594';

    protected BitcoinWallet $wallet;

    protected BitcoindFee $feeTest;

    protected BitcoindTransaction $txCheck;

    protected DenpaClient $testClient;

    protected function setUp(): void
    {
        parent::setUp();

        $config = new ConnectConfig(
            $_ENV['BITCOIN_USER'],
            $_ENV['BITCOIN_PASSWORD'],
            $_ENV['BITCOIN_HOST'],
            $_ENV['BITCOIN_PORT'],
        );

        $this->testClient = (new DenpaClient($config->toArray()))
            ->wallet($config->getWalletName());

        $this->wallet = new BitcoinWallet($config);

        $this->feeTest = new BitcoindFee(0.00003012, 2 * 60 * 10, 2);

        $this->txCheck = new BitcoindTransaction([
            'hash'             => '0a0aeeb053de0b421a5ec4ca52522205b2b6267d4aa63d132c86cdf46228a99b',
            'address'          => '2NCN4ZkJaKHQy49WxQsHYWWKn6XeiiBKqpK',
            'type'             => TransactionType::Immature,
            'amount'           => 1.56250000,
            'confirmations'    => 1,
            'block_hash'       => '5901260f01cc9f429c7f0456081aa9f7febf8718d3b48c5d38aa860a4ff87d99',
            'block_number'     => 0,
            'block_time'       => 1647455533,
            'time'             => 1647455533,
            'time_received'    => 1647455533,
            'label'            => '',
            'vout'             => 0,
            'details'          => [
                [
                    'address'  => '2NCN4ZkJaKHQy49WxQsHYWWKn6XeiiBKqpK',
                    'category' => 'immature',
                    'amount'   => 1.56250000,
                    'label'    => '',
                    'vout'     => 0
                ]
            ],
            'wallet_conflicts' => []
        ]);
    }

    public function testSend(): void
    {
        $hash = $this->wallet->send(self::DESTINATION_ADDRESS, self::SEND_AMOUNT, $this->feeTest);

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);

        $tx = $this->wallet->getTransaction($hash);

        $this->assertEquals(self::DESTINATION_ADDRESS, $tx->address);
        $this->assertEquals(self::SEND_AMOUNT, $tx->amount);
    }

    public function testGetTransactions(): void
    {
        $transactions = $this->wallet->getTransactions();
        $this->assertNotEmpty($transactions);
    }

    public function testCreateRawTransaction(): void
    {
        $tx = $this->wallet->createRawTransaction(
            outputs: [self::DESTINATION_ADDRESS => self::SEND_AMOUNT]
        );
        $this->assertEquals(self::RAW_TX, $tx);
    }

    public function testGetBalance(): void
    {
        $this->assertIsString($this->wallet->getBalance());
    }

    public function testValidAddress(): void
    {
        $this->assertTrue($this->wallet->isValidAddress(self::DESTINATION_ADDRESS));
    }

    public function testInvalidAddress(): void
    {
        $this->assertFalse($this->wallet->isValidAddress('420 ;>'));
    }

    public function testIsAvailable(): void
    {
        $this->assertTrue($this->wallet->isAvailable());
    }

    public function testCreateAndFundTransaction(): void
    {
        $tx = $this->wallet->createAndFundTransaction(
            self::DESTINATION_ADDRESS,
            self::SEND_AMOUNT,
            $this->feeTest
        );

        $this->assertIsArray($tx);
        $this->assertNotEmpty($tx);

        $this->assertArrayHasKey('hex', $tx);
        $this->assertArrayHasKey('fee', $tx);
        $this->assertArrayHasKey('changepos', $tx);

        $this->assertEquals(4.24E-6, $tx['fee']);
        $this->assertIsInt($tx['changepos']);
    }

    public function testGetClient(): void
    {
        $this->assertEquals($this->testClient, $this->wallet->getClient());
    }

    public function testEstimateSendingFee(): void
    {
        $fee = $this->wallet->estimateSendingFee(
            self::DESTINATION_ADDRESS,
            self::SEND_AMOUNT,
            $this->feeTest
        );

        $this->assertGreaterThan('0.000004', $fee);
    }

    public function testGetTransactionsSinceBlock(): void
    {
        $transactions = $this->wallet
            ->getTransactionsSinceBlock(self::BLOCKHASH);

        $this->assertNotEmpty($transactions);
        $this->assertIsString($transactions->first()?->hash);
    }

    public function testGetTransaction(): void
    {
        $tx = $this->wallet->getTransaction($this->txCheck->hash);
        $this->assertEquals($this->txCheck, $tx);
    }

    public function testGetTransactionsCount(): void
    {
        $this->assertIsInt($this->wallet->getTransactionsCount());
    }

    public function testGetNewAddress(): void
    {
        $this->assertIsString($this->wallet->getNewAddress());
    }

    public function testOwningAddress(): void
    {
        $this->assertTrue($this->wallet->isOwningAddress(self::DESTINATION_ADDRESS));
    }

    public function testNotOwningAddress(): void
    {
        $this->assertFalse($this->wallet->isOwningAddress(self::INVALID_ADDRESS));
    }

    public function testGetNetworkFees(): void
    {
        $fees = $this->wallet->getNetworkFees();
        $this->assertTrue($fees->count() > 0, "Estimated fees are empty.");
    }

    public function testFundRawTransaction(): void
    {
        $hex = $this->wallet->createRawTransaction(
            outputs: [self::DESTINATION_ADDRESS => self::SEND_AMOUNT]
        );

        $tx = $this->wallet->fundRawTransaction($hex);

        $this->assertIsArray($tx);
        $this->assertNotEmpty($tx);

        $this->assertArrayHasKey('hex', $tx);
        $this->assertArrayHasKey('fee', $tx);
        $this->assertArrayHasKey('changepos', $tx);

        $this->assertEquals(2.82E-5, $tx['fee']);
        $this->assertIsInt($tx['changepos']);
    }

    public function testGetExploreAddressLink(): void
    {
        $this->assertEquals(
            'https://blockchair.com/bitcoin/address/'.self::DESTINATION_ADDRESS,
            $this->wallet->getExploreAddressLink(self::DESTINATION_ADDRESS)
        );
    }

    public function testGetExploreTransactionLink(): void
    {
        $this->assertEquals(
            'https://blockchair.com/bitcoin/transaction/'.$this->txCheck->hash,
            $this->wallet->getExploreTransactionLink($this->txCheck->hash)
        );
    }
}

<?php

namespace Tests;

use O21\CryptoWallets\Fees\EthereumFee;
use O21\CryptoWallets\Models\EthereumTransaction;
use O21\CryptoWallets\Models\EthereumTransactionLog;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\Units\Ethereum;
use O21\CryptoWallets\Units\TransactionType;
use phpseclib\Math\BigInteger;
use Tests\Concerns\EthereumWalletTrait;
use Tests\SmartContracts\Ethereum\SmartContractTest;
use Tests\SmartContracts\Ethereum\TestContract;

class EthereumWalletTest extends TestCase
{
    use EthereumWalletTrait;

    public const PASSPHRASE = 'PUTIN_PIDOR';

    public const SEND_AMOUNT = '0.00210021';

    public const ONE_ETHER_IN_WEI = '1000000000000000000';

    protected EthereumTransaction $txCheck;

    protected EthereumTransactionReceipt $receiptCheck;

    protected EthereumFee $feeTest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEthereumClient();

        $this->txCheck = new EthereumTransaction([
            'access_list' => [],
            'block_hash' => "0x4b42e4a7b61c0ea1280f5b71d385639d0da8881f074e418d3c0711268f082bda",
            'block_number' => 14,
            'chain_id' => "0x539",
            'from' => "0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea",
            'gas' => 52263,
            'gas_price' => 164395817,
            'hash' => "0xaee73267b329196bdac4f108a5baa8a743f2dc1a1c73699cad2dba42e6481686",
            'input' => "0x",
            'max_fee_per_gas' => 375268367,
            'max_priority_fee_per_gas' => 1,
            'nonce' => 0,
            'r' => "0xd234e82c8b9033e75ecb499568edd4a6398dfb53604ce1ba3e8791b6193ca6d1",
            's' => "0x22baecf700bdea1767f4a3ec4ebe7abd7942321f10df66b122747eb19d5439a",
            'to' => "0x660ca1a6a1aeaac427433d76c79b35d845f2f27a",
            'transaction_index' => 0,
            'type' => TransactionType::Regular,
            'v' => "0x0",
            'value' => 0
        ]);

        $this->receiptCheck = new EthereumTransactionReceipt([
            'block_hash' => "0x4b42e4a7b61c0ea1280f5b71d385639d0da8881f074e418d3c0711268f082bda",
            'block_number' => 14,
            'contract_address' => null,
            'cumulative_gas_used' => 52263,
            'effective_gas_price' => 164395817,
            'from' => '0xfbc40d58581d88d194d3dc19b6ddebe65ea05fea',
            'gas_used' => 52263,
            'logs' => collect([new EthereumTransactionLog([
                'address' => '0x660ca1a6a1aeaac427433d76c79b35d845f2f27a',
                'block_hash' => '0x4b42e4a7b61c0ea1280f5b71d385639d0da8881f074e418d3c0711268f082bda',
                'block_number' => 14,
                'data' => [
                    'index' => new BigInteger(8),
                    'joke_text' => SmartContractTest::JOKE_TEXT
                ],
                'log_index' => 0,
                'removed' => false,
                'topics' => ["0xaef09189113607fcab035a14de2869f5dbd610add75d73103eb4d43474d3d294", "0x0000000000000000000000000000000000000000000000000000000000000004"],
                'transactionHash' => '0xaee73267b329196bdac4f108a5baa8a743f2dc1a1c73699cad2dba42e6481686',
                'transactionIndex' => 0
            ])]),
            'logs_bloom' => '0x00000000000000000000000000000000000000000000000000000000000000000004000000000000004000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000008000000000000000000000000000800000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000800000000000000000000000000000002000000000000000000000000000000000000000000000000000008000000000000000000000000000000000000010004000000000000000000000000000000000000000000000',
            'status' => Ethereum\TransactionReceiptStatus::Success,
            'to' => '0x660ca1a6a1aeaac427433d76c79b35d845f2f27a',
            'transaction_hash' => '0xaee73267b329196bdac4f108a5baa8a743f2dc1a1c73699cad2dba42e6481686',
            'transaction_index' => 0,
            'type' => TransactionType::Regular
        ]);

        $this->feeTest = new EthereumFee(Ethereum::Gwei->toWei(21), 60);
    }

    public function testIsAvailable(): void
    {
        $this->assertTrue($this->wallet->isAvailable());
    }

    public function testHasCoinbase(): void
    {
        $this->assertIsString($this->getCoinbase());
    }

    public function testGetBalance(): void
    {
        $this->assertIsString($this->wallet->getBalance());
    }

    public function testGetTransactionsCount(): void
    {
        $this->assertIsInt($this->wallet->getTransactionsCount());
    }

    public function testGetNewAddress(): void
    {
        $this->assertIsString($this->wallet->getNewAddress(self::PASSPHRASE));
    }

    public function testValidAddress(): void
    {
        $this->assertTrue($this->wallet->isValidAddress($this->wallet->getCoinbase()));
    }

    public function testInvalidAddress(): void
    {
        $this->assertFalse($this->wallet->isValidAddress('420 ;>'));
    }

    public function testOwningAddress(): void
    {
        $this->assertTrue($this->wallet->isOwningAddress($this->wallet->getCoinbase()));
    }

    public function testNotOwningAddress(): void
    {
        $this->assertFalse($this->wallet->isOwningAddress('Smoke weed everyyyday'));
    }

    public function testExploreLinkEqual(): void
    {
        $this->assertEquals(
            "https://etherscan.io/address/GLORY_TO_UKRAINE",
            $this->wallet->getExploreAddressLink('GLORY_TO_UKRAINE')
        );
    }

    public function testGetRecommendedFees(): void
    {
        $fees = $this->wallet->getNetworkFees();

        $this->assertTrue($fees->count() > 0, "Estimated fees are empty.");
    }

    public function testEstimateSendingFee(): void
    {
        $estimatedFee = $this->wallet->estimateSendingFee(
            '',
            self::ONE_ETHER_IN_WEI,
            $this->feeTest
        );

        $this->assertGreaterThan('0.0004', Ethereum::Ether->fromWei($estimatedFee));
    }

    public function testGetRates(): void
    {
        $this->assertIsFloat($this->wallet->getRate());
        $this->assertIsFloat($this->wallet->getBestRate());
    }

    public function testUnitCalculation(): void
    {
        $this->assertEquals(self::ONE_ETHER_IN_WEI, Ethereum::Ether->toWei(1));
        $this->assertEquals(self::ONE_ETHER_IN_WEI, Ethereum::Gwei->toWei(1000000000));
        $this->assertEquals(self::ONE_ETHER_IN_WEI, Ethereum::Mwei->toWei(1000000000000));
        $this->assertEquals(self::ONE_ETHER_IN_WEI, Ethereum::Kwei->toWei(1000000000000000));
    }

    public function testEstimateAndSending(): void
    {
        $client = $this->wallet;

        $accounts = $client->getAccounts();
        if (! isset($accounts[1])) {
            $this->markTestSkipped("Can't test transaction sending, because server has only one account.");
        }

        $sendAmountInWei = Ethereum::Ether->toWei(self::SEND_AMOUNT);

        $hash = $client->send(
            $accounts[1],
            $sendAmountInWei,
            $this->feeTest
        );

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);

        $transaction = $client->getTransaction($hash);

        $this->assertEquals($accounts[1], $transaction->address);
        $this->assertEquals($client->getCoinbase(), $transaction->from);
        $this->assertEquals($sendAmountInWei, $transaction->amount);
    }

    public function testGetTransactions(): void
    {
        $transactions = $this->wallet->getTransactions();
        $this->assertNotEmpty($transactions);
    }

    public function testGetTransactionsInBlock(): void
    {
        $transactions = $this->wallet
            ->getTransactionsInBlock(1);

        $this->assertNotEmpty($transactions);
        $this->assertIsString($transactions->first()?->hash);
    }

    public function testGetTransactionsSinceBlock(): void
    {
        $transactions = $this->wallet
            ->getTransactionsSinceBlock(1);

        $this->assertNotEmpty($transactions);
        $this->assertIsString($transactions->first()?->hash);
    }

    public function testGetExploreTransactionLink(): void
    {
        $this->assertEquals(
            "https://etherchain.org/tx/".$this->txCheck->hash,
            $this->wallet->getExploreTransactionLink($this->txCheck->hash)
        );
    }

    public function testGetInvalidTransaction(): void
    {
        $tx = $this->wallet->getTransaction('0x98749d64c420d16cefa0054e1ae89a5450a49a71cf98f7acfcf0b624030558a3');
        $this->assertNull($tx);
    }

    public function testGetTransactionReceipt(): void
    {
        $receipt = $this->wallet->getTransactionReceipt($this->txCheck->hash, TestContract::class);
        $this->assertEquals($this->receiptCheck, $receipt);
    }

    public function testGetInvalidTransactionReceipt(): void
    {
        $tx = $this->wallet->getTransactionReceipt('0x98749d64c420d16cefa0054e1ae89a5450a49a71cf98f7acfcf0b624030558a3');
        $this->assertNull($tx);
    }

    protected function getCoinbase(): string
    {
        return $this->wallet->getCoinbase();
    }
}

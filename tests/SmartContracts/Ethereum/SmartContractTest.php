<?php

namespace Tests\SmartContracts\Ethereum;

use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;
use O21\CryptoWallets\SmartContracts\Ethereum\Filters\LogsFilter;
use Tests\Concerns\EthereumWalletTrait;
use Tests\TestCase;

class SmartContractTest extends TestCase
{
    use EthereumWalletTrait;

    public const JOKE_TEXT = 'somejoke';
    public const PHONE_NUMBER = 7777;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEthereumClient();
    }

    public function testDeploy(): void
    {
        $client = $this->wallet;

        $smartContract = $client->getSmartContract(TestContract::class);

        $receipt = $smartContract->deploy(
            new DeployParams(new EthereumCall($client->getCoinbase())),
            $error
        );

        $this->assertNull($error);
        $this->assertNotEmpty($smartContract->getAddress());
        $this->assertInstanceOf(EthereumTransactionReceipt::class, $receipt);

        $_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS'] = $smartContract->getAddress();
    }

    public function testCallJokeMethod(): void
    {
        $smartContract = $this->getTestContract($_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS']);

        $hash = $smartContract->joke(self::JOKE_TEXT);

        $this->assertIsString($hash);
    }

    public function testEstimateGasJokeMethod(): void
    {
        $smartContract = $this->getTestContract($_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS']);

        $gas = $smartContract->estimateJokeGas(self::JOKE_TEXT);

        $this->assertGreaterThan('50000', $gas);
    }

    public function testCallAddPhoneNumberMethod(): void
    {
        $smartContract = $this->getTestContract($_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS']);

        $hash = $smartContract->addPhoneNumber(self::PHONE_NUMBER);

        $this->assertIsString($hash);
    }

    public function testGetFirstAddedJoke(): void
    {
        $smartContract = $this->getTestContract($_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS']);

        $this->assertEquals(self::JOKE_TEXT, $smartContract->getJoke(0));
    }

    public function testDecodeLogParameters(): void
    {
        $smartContract = $this->getTestContract($_ENV['ETHEREUM_SMART_CONTRACT_TEST_ADDRESS']);

        $filter = new LogsFilter(fromBlock: 1);
        $logs = $smartContract->getLogs($filter);

        $this->assertNotEmpty($logs);

        $this->assertEquals(self::JOKE_TEXT, $logs->first()->data['text']);
        $this->assertEquals(self::PHONE_NUMBER, (int)(string)$logs->get(1)->data['_number']);
    }

    protected function getTestContract(?string $address = null): TestContract
    {
        $contract = $this->wallet->getSmartContract(TestContract::class);
        if ($address) {
            $contract->setAddress($address);
        }
        return $contract;
    }
}
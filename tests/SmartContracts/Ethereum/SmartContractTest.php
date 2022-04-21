<?php

namespace Tests\SmartContracts\Ethereum;

use O21\CryptoWallets\Models\EthereumCall;
use O21\CryptoWallets\Models\EthereumTransactionReceipt;
use O21\CryptoWallets\SmartContracts\Ethereum\DeployParams;
use Tests\Concerns\EthereumWalletTrait;
use Tests\TestCase;

class SmartContractTest extends TestCase
{
    use EthereumWalletTrait;

    private const DEPLOYED_CONTRACT_ADDRESS = '0x660ca1a6a1aeaac427433d76c79b35d845f2f27a';

    public const JOKE_TEXT = 'somejoke';

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
    }

    public function testCallJokeMethod(): void
    {
        $smartContract = $this->getTestContract(self::DEPLOYED_CONTRACT_ADDRESS);

        $hash = $smartContract->joke(self::JOKE_TEXT);

        $this->assertIsString($hash);
    }

    public function testGetFirstAddedJoke(): void
    {
        $smartContract = $this->getTestContract(self::DEPLOYED_CONTRACT_ADDRESS);

        $this->assertEquals(self::JOKE_TEXT, $smartContract->getJoke(0));
    }

    public function testDecodeLogParameters(): void
    {
        $smartContract = $this->getTestContract(self::DEPLOYED_CONTRACT_ADDRESS);

        $logs = $smartContract->getLogs();

        $this->assertNotEmpty($logs);
        $this->assertEquals(self::JOKE_TEXT, $logs->first()->data['joke_text']);
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
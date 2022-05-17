<?php

namespace Tests\SmartContracts\Ethereum;

use O21\CryptoWallets\SmartContracts\Ethereum\AbstractSmartContract;

class TestContract extends AbstractSmartContract
{
    public const ABI_JSON = <<<ABI
[{"anonymous":false,"inputs":[{"indexed":true,"internalType":"uint256","name":"_index","type":"uint256"},{"indexed":false,"internalType":"string","name":"text","type":"string"}],"name":"NewJoke","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"_number","type":"uint256"}],"name":"NewPhoneNumber","type":"event"},{"inputs":[{"internalType":"uint256","name":"_number","type":"uint256"}],"name":"addPhoneNumber","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"index","type":"uint256"}],"name":"getJoke","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"string","name":"_joke_text","type":"string"}],"name":"joke","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"joke_count","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"jokes","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"}]
ABI;

    public const BYTECODE = <<<byteCode
0x608060405234801561001057600080fd5b50610726806100206000396000f3fe608060405234801561001057600080fd5b50600436106100575760003560e01c806370eea42f1461005c5780639d43829a14610078578063a64d499814610094578063ac647420146100c4578063b99505ec146100f4575b600080fd5b61007660048036038101906100719190610424565b610112565b005b610092600480360381019061008d91906104a7565b61018e565b005b6100ae60048036038101906100a991906104a7565b6101c8565b6040516100bb919061056d565b60405180910390f35b6100de60048036038101906100d991906104a7565b610268565b6040516100eb919061056d565b60405180910390f35b6100fc61030c565b604051610109919061059e565b60405180910390f35b818160008060015481526020019081526020016000209190610135929190610312565b506001547faef09189113607fcab035a14de2869f5dbd610add75d73103eb4d43474d3d294838360405161016a9291906105f5565b60405180910390a26001600081548092919061018590610648565b91905055505050565b7fc8cbfc8f9227f1dc735fb556f1a569f73310cf907ab0ab231d6f400409b338a9816040516101bd919061059e565b60405180910390a150565b600060205280600052604060002060009150905080546101e7906106bf565b80601f0160208091040260200160405190810160405280929190818152602001828054610213906106bf565b80156102605780601f1061023557610100808354040283529160200191610260565b820191906000526020600020905b81548152906001019060200180831161024357829003601f168201915b505050505081565b60606000808381526020019081526020016000208054610287906106bf565b80601f01602080910402602001604051908101604052809291908181526020018280546102b3906106bf565b80156103005780601f106102d557610100808354040283529160200191610300565b820191906000526020600020905b8154815290600101906020018083116102e357829003601f168201915b50505050509050919050565b60015481565b82805461031e906106bf565b90600052602060002090601f0160209004810192826103405760008555610387565b82601f1061035957803560ff1916838001178555610387565b82800160010185558215610387579182015b8281111561038657823582559160200191906001019061036b565b5b5090506103949190610398565b5090565b5b808211156103b1576000816000905550600101610399565b5090565b600080fd5b600080fd5b600080fd5b600080fd5b600080fd5b60008083601f8401126103e4576103e36103bf565b5b8235905067ffffffffffffffff811115610401576104006103c4565b5b60208301915083600182028301111561041d5761041c6103c9565b5b9250929050565b6000806020838503121561043b5761043a6103b5565b5b600083013567ffffffffffffffff811115610459576104586103ba565b5b610465858286016103ce565b92509250509250929050565b6000819050919050565b61048481610471565b811461048f57600080fd5b50565b6000813590506104a18161047b565b92915050565b6000602082840312156104bd576104bc6103b5565b5b60006104cb84828501610492565b91505092915050565b600081519050919050565b600082825260208201905092915050565b60005b8381101561050e5780820151818401526020810190506104f3565b8381111561051d576000848401525b50505050565b6000601f19601f8301169050919050565b600061053f826104d4565b61054981856104df565b93506105598185602086016104f0565b61056281610523565b840191505092915050565b600060208201905081810360008301526105878184610534565b905092915050565b61059881610471565b82525050565b60006020820190506105b3600083018461058f565b92915050565b82818337600083830152505050565b60006105d483856104df565b93506105e18385846105b9565b6105ea83610523565b840190509392505050565b600060208201905081810360008301526106108184866105c8565b90509392505050565b7f4e487b7100000000000000000000000000000000000000000000000000000000600052601160045260246000fd5b600061065382610471565b91507fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff820361068557610684610619565b5b600182019050919050565b7f4e487b7100000000000000000000000000000000000000000000000000000000600052602260045260246000fd5b600060028204905060018216806106d757607f821691505b6020821081036106ea576106e9610690565b5b5091905056fea2646970667358221220bba1f649855b6649b79663f09233e367e6d82bb69945675ea68980955e329b6d64736f6c634300080d0033
byteCode;

    public static function getAbi(): array
    {
        return json_decode(
            self::ABI_JSON,
            false,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public static function getByteCode(): string
    {
        return self::BYTECODE;
    }

    public function joke(
        string $text,
        ?string $from = null,
        ?string &$error = null
    ): ?string {
        return $this->sendContractMethod('joke', [$text], $from, $error);
    }

    public function getJoke(int $index): ?string
    {
        return first($this->call('getJoke', true, $index));
    }

    public function addPhoneNumber(
        int $number,
        ?string $from = null,
        ?string &$error = null
    ): ?string {
        return $this->sendContractMethod('addPhoneNumber', [$number], $from, $error);
    }
}
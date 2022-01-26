## Installation 

Run ```composer require 021/crypto-wallets```

## Requirements

- PHP 7.1+

## Usage

### Import wallet 

```use O21\CryptoWallets\BitcoinWallet;``` 
or 
```use O21\CryptoWallets\LitecoinWallet;``` 

### Connect to wallet 

```
use O21\CryptoWallets\Configs\BitcoindConfig;

$wallet = new BitcoinWallet(BitcoindConfig::fill(
    'username',
    'password',
    '127.0.0.1',
    18333
));
```

### Check connection 
```$wallet->isAvailable()```

### Wallet
```
// Return wallet balance 
$wallet->getBalance()

// Return transactions count for wallet
$wallet->getTransactionsCount()

// Return typical transaction size for wallet (Constant)
$wallet->getTypicalTransactionSize()
``` 

### Rates

By default, method ```getRate()``` will return result from binance.com

```
use O21\CryptoWallets\Contracts\WalletRate;

// Return USD rate for Bitcoin 
$wallet->getRate() 

// Return EUR rate for Bitcoin 
$wallet->getRate('EUR') 

// Return EUR rate for Bitcoin from blockchain.com
$wallet->getRate('EUR', WalletRate::RATE_BLOCKCHAIN)

// Return best rate for last 30 minutes 
$wallet->getBestRate('USD', 30)
```

### Addresses
```
// Return new addresses
$wallet->getNewAddress()

// Return is address valid 
$wallet->validateAddress('wallet_address')

// Return link to blockchair.com for explain address
$wallet->getExploreAddressLink('wallet_address')
```

> To be continued...

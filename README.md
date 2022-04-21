# Installation 

Run ```composer require 021/crypto-wallets```

# Requirements

- PHP 8.1+

# Wallet interface

### \O21\CryptoWallets\Interfaces\WalletInterface
  `__construct(\O21\CryptoWallets\Interfaces\ConnectConfigInterface $config)`

  `isAvailable(): bool` - check is RPC client available

  `getBalance(): string` - get wallet balance

  `getNewAddress($config = null): string` - get new address

  `isValidAddress(string $address): bool` - check is an address is valid

  `isOwningAddress(string $address): bool` - checks if an address belongs to a wallet
  
  `getExploreAddressLink(string $address): string` - returns a link to blockchain explorer for the address

  ```
  getRate(
        string $currency = 'USD', 
        ?\O21\CryptoWallets\Interfaces\RateProviderInterface $provider = null
  ): float
  // Returns the cryptocurrency exchange rate for the selected currency
  ```  
  ```
  getBestRate(
      string $currency = 'USD',
      int $limit = 60,
      \O21\CryptoWallets\Units\RateInterval $interval = RateInterval::Minutes,
      ?\O21\CryptoWallets\Interfaces\RateProviderInterface $provider = null
  ): float
  // Returns the best cryptocurrency rate for the selected currency for a given period of time
  ```
  ```
  estimateSendingFee(
      string $to,
      string $value,
      \O21\CryptoWallets\Interfaces\FeeInterface|string $fee
  ): string
  // Estimates the fee amount required to send a transaction
  ```
  ```
  send(
      string $to,
      string $value,
      \O21\CryptoWallets\Interfaces\FeeInterface|string $fee
  ): string
  // Send funds from a wallet
  ```
  `getTransaction(string $hash): ?\O21\CryptoWallets\Interfaces\TransactionInterface` - returns transaction from a wallet
  
  `getTransactions(int $count = 50, int $skip = 0): \Illuminate\Support\Collection;` - returns transactions from a wallet

  `getTransactionsSinceBlock(string $block = ''): \Illuminate\Support\Collection;` - returns transactions from a wallet
  
  `getTransactionsCount(): int` - returns transactions count on a wallet
  
  `getExploreTransactionLink(string $hash): string` - returns a link to blockchain explorer for the address

  ```
  /**
   * @return \Illuminate\Support\Collection<\O21\CryptoWallets\Interfaces\FeeInterface>
   */
  public function getNetworkFees(): Collection;
  // Returns the recommended fees for the transaction
  ```

  `getDefaultBestRateLimit(): int` - returns the default value for the period in the `getBestRate` function

  `getTypicalTransactionSize(): int` - returns typical transaction size for a wallet

  `getSymbol(): string` - returns symbol of a wallet 

Also, some wallets have their own unique methods. Explore the interfaces `\O21\CryptoWallets\Interfaces\BitcoindWalletInterface` and `\O21\CryptoWallets\Interfaces\EthereumWalletInterface`

## Currently available wallets

`\O21\CryptoWallets\BitcoinWallet` for bitcoin

`\O21\CryptoWallets\LitecoinWallet` for litecoin

`\O21\CryptoWallets\EthereumWallet` for ethereum
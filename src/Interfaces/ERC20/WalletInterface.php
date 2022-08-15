<?php

namespace O21\CryptoWallets\Interfaces\ERC20;

interface WalletInterface extends \O21\CryptoWallets\Interfaces\WalletInterface
{
    /**
     * @param  string  $spender
     * @param  string|int  $amount  in wei
     * @return string transaction hash
     */
    public function approveToSend(string $spender, string|int $amount): string;

    /**
     * @param  string  $owner
     * @param  string  $spender
     * @return string in token unit
     */
    public function getAllowance(string $owner, string $spender): string;

    public function fromWeiToToken($value): string;

    public function fromTokenToWei($value): string;

    public function getContractAddress(): string;
}
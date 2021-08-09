<?php

declare(strict_types=1);

namespace App;

final class VendingMachine
{
    private array $items;
    private Wallet $clientWallet;
    private Wallet $vendingMachineWallet;

    public function __construct()
    {
        $this->clientWallet = new Wallet();
        $this->vendingMachineWallet = new Wallet();
    }

    public function add(string $name, float $price, int $quantity, int $selector): void
    {
        $this->items[$selector] = Item::create($name, $price, $quantity);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->clientWallet->add($coin);
    }

    public function select(int $selector): ItemSold
    {
        $this->checkEnoughMoneyForSelection($selector);
        /** @var Item $itemSelected */
        $itemSelected = $this->items[$selector];

        return new ItemSold(
            $this->items[$selector]->name(),
            $this->calculateChange($itemSelected)
        );
    }

    public function returnCoins(): array
    {
        return $this->clientWallet->toArray();
    }

    private function checkEnoughMoneyForSelection(int $selector): void
    {
        if ($this->items[$selector]->price() > $this->clientWallet->totalAmount()) {
            throw new NotEnoughMoneyForItemException();
        }
    }

    private function calculateChange(Item $itemSelected): Wallet
    {
        if ($this->clientWallet->totalAmount() === $itemSelected->price()) {
            return new Wallet();
        }

        $this->vendingMachineWallet = $this->vendingMachineWallet->addWallet($this->clientWallet);
        $changeAmount = $this->clientWallet->totalAmount() - $itemSelected->price();
        return $this->vendingMachineWallet->obtainWalletFromAmount($changeAmount);
    }
}

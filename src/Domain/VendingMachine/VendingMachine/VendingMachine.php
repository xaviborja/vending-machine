<?php

declare(strict_types=1);

namespace App\Domain\VendingMachine\VendingMachine;

use App\Domain\VendingMachine\Coin\Coin;
use App\Domain\VendingMachine\Wallet\Wallet;

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
        $this->checkItemAvailable($selector);
        $this->checkEnoughMoneyForSelection($selector);
        $this->vendingMachineWallet = $this->vendingMachineWallet->addWallet($this->clientWallet);
        /** @var Item $itemSelected */
        $itemSelected = $this->items[$selector];
        $changeWallet = $this->calculateChange($itemSelected);
        $this->clientWallet = new Wallet();

        return new ItemSold(
            $this->items[$selector]->name(),
            $changeWallet
        );
    }

    public function returnCoins(): array
    {
        $coinsToReturn = $this->clientWallet->toArray();
        $this->clientWallet = new Wallet();
        return $coinsToReturn;
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

        $changeAmount = $this->clientWallet->totalAmount() - $itemSelected->price();
        return $this->vendingMachineWallet->obtainWalletForChange($changeAmount);
    }

    public function totalAmount(): float
    {
        return $this->vendingMachineWallet->totalAmount();
    }

    public function addCoinForChange(Coin $coin, int $quantity): void
    {
        $this->vendingMachineWallet->add($coin, $quantity);
    }

    public function updateItemQuantityBySelector(int $selector, int $quantity): void
    {
        /** @var Item $item */
        $item = $this->items[$selector];
        $item->updateQuantity($quantity);
        $this->items[$selector] = $item;
    }

    private function checkItemAvailable(int $selector): void
    {
        /** @var Item $item */
        $item = $this->items[$selector];
        if ($item->quantity() === 0) {
           throw new ItemNotAvailableException();
        }
    }
}

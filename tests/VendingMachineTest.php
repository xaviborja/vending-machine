<?php

declare(strict_types=1);

namespace App\Tests;

use App\Coin;
use App\NotEnoughMoneyForItemException;
use App\VendingMachine;
use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase
{
    public function testShouldGetItemWithExactPrice(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(1));
        $itemSold = $vendingMachine->select(1);
        self::assertEquals('Juice', $itemSold->name());
        self::assertEquals(0, $itemSold->change()->totalAmount());
    }

    public function testShouldReturnCoins(): void
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->insertCoin(new Coin(0.10));
        self::assertEquals([1, 0.10], $vendingMachine->returnCoins());
    }

    public function testShouldGetItemWithChange(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->insertCoin(new Coin(0.10));
        $vendingMachine->insertCoin(new Coin(0.05));
        $itemSold = $vendingMachine->select(1);
        self::assertEquals('Juice', $itemSold->name());
        self::assertEquals(0.15, $itemSold->change()->totalAmount());
    }

    public function testShouldNotBuyWithoutEnoughMoney(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(0.10));
        $this->expectException(NotEnoughMoneyForItemException::class);
        $vendingMachine->select(1);
    }
}

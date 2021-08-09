<?php

declare(strict_types=1);

namespace App\Tests\Domain\VendingMachine\VendingMachine;

use App\Domain\VendingMachine\Coin\Coin;
use App\Domain\VendingMachine\Coin\InvalidCoinException;
use App\Domain\VendingMachine\VendingMachine\NotEnoughMoneyForItemException;
use App\Domain\VendingMachine\VendingMachine\VendingMachine;
use App\Domain\VendingMachine\Wallet\NotEnoughChangeException;
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
        $vendingMachine->add('Soda', 1.50, 10, 2);

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->insertCoin(new Coin(0.10));
        $vendingMachine->insertCoin(new Coin(0.05));
        $itemSold = $vendingMachine->select(1);
        self::assertEquals('Juice', $itemSold->name());
        self::assertEquals(0.15, $itemSold->change()->totalAmount());

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->insertCoin(new Coin(0.10));
        $vendingMachine->insertCoin(new Coin(0.10));
        $vendingMachine->insertCoin(new Coin(0.25));
        $vendingMachine->insertCoin(new Coin(0.25));
        $itemSold = $vendingMachine->select(2);
        self::assertEquals([0.10, 0.10], $itemSold->change()->toArray());
    }

    public function testShouldNotBuyWithoutEnoughMoney(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(0.10));
        $this->expectException(NotEnoughMoneyForItemException::class);
        $vendingMachine->select(1);
    }

    public function testShouldNotAcceptInvalidCoin(): void
    {
        $vendingMachine = new VendingMachine();

        $this->expectException(InvalidCoinException::class);
        $vendingMachine->insertCoin(new Coin(0.20));
    }

    public function testTotalAmountAfterBuyAnItem(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->select(1);

        self::assertEquals(1, $vendingMachine->totalAmount());
    }

    public function testShouldNotSellWhenDoesNotHaveEnoughChange(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Water', 0.65, 1, 1);
        $vendingMachine->insertCoin(new Coin(1));
        $this->expectException(NotEnoughChangeException::class);
        $vendingMachine->select(1);
    }

    public function testShouldClearClientWalletAfterBuy(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->select(1);
        self::assertEquals([], $vendingMachine->returnCoins());
        $this->expectException(NotEnoughMoneyForItemException::class);
        $vendingMachine->select(1);
    }

    public function testShouldClearClientWalletAfterReturnCoins(): void
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertCoin(new Coin(1));
        $vendingMachine->insertCoin(new Coin(0.10));
        self::assertEquals([1, 0.10], $vendingMachine->returnCoins());
        self::assertEquals([], $vendingMachine->returnCoins());
    }
}

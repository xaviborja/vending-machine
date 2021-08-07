<?php

declare(strict_types=1);

namespace App\Tests;

use App\VendingMachine;
use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase
{
    public function testShouldGetItemWithExactPrice(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin('1');
        $itemSold = $vendingMachine->select(1);
        self::assertEquals('Juice', $itemSold->name());
    }

    public function testShouldReturnCoins(): void
    {
        $vendingMachine = new VendingMachine();

        $vendingMachine->insertCoin('1');
        $vendingMachine->insertCoin('0.10');
        self::assertEquals(['1', '0.10'], $vendingMachine->returnCoins());
    }

    public function testShouldGetItemWithChange(): void
    {
        $vendingMachine = new VendingMachine();
        $vendingMachine->add('Juice', 1, 1, 1);

        $vendingMachine->insertCoin('1');
        $vendingMachine->insertCoin('1');
        $itemSold = $vendingMachine->select(1);
        self::assertEquals('Juice', $itemSold->name());
        self::assertEquals('1', $itemSold->change());
    }
}

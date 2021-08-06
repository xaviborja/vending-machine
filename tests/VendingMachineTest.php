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

        $vendingMachine->insertCoin(1);
        self::assertEquals('Juice', $vendingMachine->select(1));
    }
}

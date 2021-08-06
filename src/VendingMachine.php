<?php

declare(strict_types=1);

namespace App;

final class VendingMachine
{
    private array $items;
    private array $coinsInserted = [
        1 => 0,
    ];

    public function add(string $name, float $price, int $quantity, int $selector): void
    {
        $this->items[$selector] = Item::create($name, $price, $quantity);
    }

    public function insertCoin(float $coin): void
    {
        $this->coinsInserted[$coin]++;
    }

    public function select(int $selector): string
    {
        return $this->items[$selector]->name();
    }


}

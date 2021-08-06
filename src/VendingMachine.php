<?php

declare(strict_types=1);

namespace App;

final class VendingMachine
{
    private array $items;
    private array $coinsInserted;

    public function add(string $name, float $price, int $quantity, int $selector): void
    {
        $this->items[$selector] = Item::create($name, $price, $quantity);
    }

    public function insertCoin(string $coin): void
    {
        isset($this->coinsInserted[$coin]) ? $this->coinsInserted[$coin]++ : $this->coinsInserted[$coin] = 1;
    }

    public function select(int $selector): string
    {
        return $this->items[$selector]->name();
    }

    public function returnCoins(): array
    {
        $coinsToReturn = [];
        foreach ($this->coinsInserted as $coin => $quantity) {
            for ($i = 1; $i<=$quantity; $i++) {
                $coinsToReturn[] = $coin;
            }
        }

        return $coinsToReturn;
    }
}

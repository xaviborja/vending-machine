<?php

declare(strict_types=1);

namespace App\Domain\VendingMachine\VendingMachine;

final class Item
{
    private string $name;
    private float $price;
    private int $quantity;

    public function __construct(string $name, float $price, int $quantity)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public static function create(string $name, float $price, int $quantity): self
    {
        return new self($name, $price, $quantity);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): float
    {
        return $this->price;
    }
}

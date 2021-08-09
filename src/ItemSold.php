<?php

declare(strict_types=1);

namespace App;

final class ItemSold
{
    private string $name;
    private Wallet $change;

    public function __construct(string $name, Wallet $change)
    {
        $this->name = $name;
        $this->change = $change;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function change(): Wallet
    {
        return $this->change;
    }



}

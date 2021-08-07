<?php

declare(strict_types=1);

namespace App;

final class ItemSold
{
    private string $name;
    private string $change;

    public function __construct(string $name, string $change)
    {
        $this->name = $name;
        $this->change = $change;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function change(): string
    {
        return $this->change;
    }



}

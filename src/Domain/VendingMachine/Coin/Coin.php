<?php

declare(strict_types=1);

namespace App\Domain\VendingMachine\Coin;

final class Coin
{
    private float $value;

    private const VALID_VALUES = [
        0.05,
        0.10,
        0.25,
        1
    ];

    public function __construct(float $value)
    {
        $this->checkIsValid($value);
        $this->value = $value;
    }

    public function value(): float
    {
        return $this->value;
    }

    private function checkIsValid(float $value): void
    {
        if (!in_array($value, self::VALID_VALUES, false)) {
            throw new InvalidCoinException();
        }
    }

}

<?php

declare(strict_types=1);

namespace App\Domain\VendingMachine\Wallet;

use App\Domain\VendingMachine\Coin\Coin;

final class Wallet
{
    private array $coins = [];

    public function __construct(array $coins = [])
    {
        if (!empty($coins)) {
            foreach ($coins as $coin => $quantity) {
                $this->add(new Coin((float)$coin), $quantity);
            }
        }
    }
    public function totalAmount(): float
    {
        $total = 0;
        foreach ($this->coins as $coin => $quantity) {
            $total+= (float)$coin * $quantity;
        }

        return $total;
    }

    public function add(Coin $coin, int $quantity = 1): void
    {
        !isset($this->coins[(string)$coin->value()]) ? $this->coins[(string)$coin->value()] = $quantity : $this->coins[(string)$coin->value()] += $quantity;
    }

    public function toArray(): array
    {
        $coinsToReturn = [];
        foreach ($this->coins as $coin => $quantity) {
            for ($i = 1; $i<=$quantity; $i++) {
                $coinsToReturn[] = $coin;
            }
        }

        return $coinsToReturn;
    }

    public function addWallet(self $wallet): self
    {
        foreach ($wallet->coins() as $coin => $quantity) {
            $this->add(new Coin((float)$coin), $quantity);
        }

        return new self($this->coins());
    }

    private function coins(): array
    {
        return $this->coins;
    }

    public function obtainWalletForChange(float $amount): self
    {
        $coins = $this->coins();
        krsort($coins);
        $wallet = new self();
        $amount = round($amount, 2);
        foreach ($coins as $coin => $quantity) {
            $coinRounded = round((float)$coin, 2);

            for ($i = 1; $i<=$quantity; $i++) {
                $pendingAmountBiggerOrEqualToCoin = $coinRounded < $amount || abs($coinRounded - $amount) < 0.0001;
                if ($pendingAmountBiggerOrEqualToCoin) {
                    $wallet->add(new Coin($coinRounded));
                    $amount -= $coinRounded;
                } elseif ($wallet->totalAmount() < $amount) {
                    break;
                } else {
                    break 2;
                }
            }
        }

        if(round($amount, 2) > 0) {
            throw new NotEnoughChangeException();
        }

        return $wallet;
    }


}

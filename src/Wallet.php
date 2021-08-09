<?php

declare(strict_types=1);

namespace App;

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

    public function obtainWalletFromAmount(float $amount): self
    {
        $coins = $this->coins();
        krsort($coins);
        $wallet = new self();

        foreach ($coins as $coin => $quantity) {
            for ($i = 1; $i<=$quantity; $i++) {
                if ((float)$coin <= $amount) {
                    $wallet->add(new Coin((float)$coin));
                    $amount -= (float)$coin;
                } elseif ($wallet->totalAmount() < $amount) {
                    break;
                } else {
                    break 2;
                }
            }
        }

        return $wallet;
    }


}

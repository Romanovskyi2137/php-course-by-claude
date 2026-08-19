<?php

declare(strict_types=1);

// Тема 21: помилки та винятки — референсний розв'язок.

class InsufficientFundsException extends \RuntimeException
{
}

class Account
{
    public function __construct(private float $balance = 0.0)
    {
    }

    public function withdraw(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientFundsException(
                "Недостатньо коштів: запит {$amount}, наявно {$this->balance}"
            );
        }

        $this->balance -= $amount;
    }

    public function balance(): float
    {
        return $this->balance;
    }
}

function safe_withdraw(Account $account, float $amount): bool
{
    try {
        $account->withdraw($amount);

        return true;
    } catch (InsufficientFundsException) {
        return false;
    }
}

function safe_divide(int $a, int $b): ?float
{
    try {
        return $a / $b;
    } catch (\DivisionByZeroError) {
        return null;
    }
}

<?php

declare(strict_types=1);

// Тема 21: помилки та винятки. Опис завдання — у task.md.

// TODO (вимога 1): власний клас винятку.
class InsufficientFundsException extends \RuntimeException
{
}

// TODO (вимога 2): рахунок, що кидає InsufficientFundsException при нестачі коштів.
class Account
{
    public function __construct(float $balance = 0.0)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function withdraw(float $amount): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function balance(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): try/catch навколо withdraw(), повертає bool, не пропускає виняток далі.
function safe_withdraw(Account $account, float $amount): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): $a / $b, null при діленні на нуль (DivisionByZeroError).
function safe_divide(int $a, int $b): ?float
{
    throw new \RuntimeException('Не реалізовано');
}

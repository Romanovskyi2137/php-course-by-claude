<?php

declare(strict_types=1);

// Тема 16: інтерфейси та поліморфізм. Опис завдання — у task.md.

// TODO (вимога 1): інтерфейс Priced з методом price(): float.
interface Priced
{
}

// TODO (вимога 2): Book implements Priced.
class Book implements Priced
{
    public function __construct(float $cost)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function price(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): Ticket implements Priced, price() = basePrice + fee.
class Ticket implements Priced
{
    public function __construct(float $basePrice, float $fee)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function price(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): сума price() усіх елементів, InvalidArgumentException якщо не Priced.
function total_price(array $items): float
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 5): елемент з найбільшою price().
function most_expensive(array $items): Priced
{
    throw new \RuntimeException('Не реалізовано');
}

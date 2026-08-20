<?php

declare(strict_types=1);

namespace App;

// Тема 38: Composer та PSR-4. Опис завдання — у task.md.

// TODO (вимога 3): контейнер товарів Money, total() через Money::add().
final class Cart
{
    public function add(Money $item): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function total(): Money
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

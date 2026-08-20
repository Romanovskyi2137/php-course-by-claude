<?php

declare(strict_types=1);

namespace App;

// Тема 38: Composer та PSR-4 — референсний розв'язок.

final class Cart
{
    private array $items = [];

    public function add(Money $item): void
    {
        $this->items[] = $item;
    }

    public function total(): Money
    {
        if ($this->items === []) {
            throw new \RuntimeException('Кошик порожній');
        }

        return array_reduce(
            array_slice($this->items, 1),
            static fn (Money $carry, Money $item): Money => $carry->add($item),
            $this->items[0]
        );
    }
}

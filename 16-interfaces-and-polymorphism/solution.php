<?php

declare(strict_types=1);

// Тема 16: інтерфейси та поліморфізм — референсний розв'язок.

interface Priced
{
    public function price(): float;
}

class Book implements Priced
{
    public function __construct(private float $cost)
    {
    }

    public function price(): float
    {
        return $this->cost;
    }
}

class Ticket implements Priced
{
    public function __construct(private float $basePrice, private float $fee)
    {
    }

    public function price(): float
    {
        return $this->basePrice + $this->fee;
    }
}

function total_price(array $items): float
{
    $total = 0.0;

    foreach ($items as $item) {
        if (!$item instanceof Priced) {
            throw new \InvalidArgumentException('Очікував обʼєкт, що реалізує Priced');
        }

        $total += $item->price();
    }

    return $total;
}

function most_expensive(array $items): Priced
{
    $best = $items[0];

    foreach ($items as $item) {
        if ($item->price() > $best->price()) {
            $best = $item;
        }
    }

    return $best;
}

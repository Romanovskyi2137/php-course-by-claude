<?php

declare(strict_types=1);

namespace App;

// Тема 38: Composer та PSR-4 — референсний розв'язок.

final class Money
{
    public function __construct(
        public readonly int $cents,
        public readonly string $currency,
    ) {
    }

    public function add(Money $other): self
    {
        if ($other->currency !== $this->currency) {
            throw new \InvalidArgumentException('Не можна додати гроші різних валют');
        }

        return new self($this->cents + $other->cents, $this->currency);
    }

    public function format(): string
    {
        return number_format($this->cents / 100, 2, '.', '') . ' ' . $this->currency;
    }
}

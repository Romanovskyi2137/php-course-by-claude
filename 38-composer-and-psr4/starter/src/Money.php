<?php

declare(strict_types=1);

namespace App;

// Тема 38: Composer та PSR-4. Опис завдання — у task.md.

// TODO (вимога 2): readonly Money, add() і format().
final class Money
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function add(Money $other): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function format(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

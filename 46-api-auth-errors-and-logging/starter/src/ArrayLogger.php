<?php

declare(strict_types=1);

namespace App;

// TODO (вимога 4): PSR-3-подібний логер у пам'яті.
final class ArrayLogger implements Logger
{
    public function info(string $message, array $context = []): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function error(string $message, array $context = []): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function entries(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

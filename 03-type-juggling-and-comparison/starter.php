<?php

declare(strict_types=1);

// Тема 03: жонглювання типами та порівняння. Опис завдання — у task.md.

// TODO (вимога 1): приведи значення до bool за правилами PHP.
function is_truthy(mixed $value): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): строге порівняння ===.
function strict_equals(mixed $a, mixed $b): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): нестроге порівняння ==.
function loose_equals(mixed $a, mixed $b): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): 'missing' / 'null' / 'present' залежно від стану ключа.
function key_status(array $data, string $key): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 5): spaceship-порівняння <=>.
function compare(int|float $a, int|float $b): int
{
    throw new \RuntimeException('Не реалізовано');
}

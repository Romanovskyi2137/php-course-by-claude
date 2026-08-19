<?php

declare(strict_types=1);

// Тема 03: жонглювання типами та порівняння — референсний розв'язок.

function is_truthy(mixed $value): bool
{
    return (bool) $value;
}

function strict_equals(mixed $a, mixed $b): bool
{
    return $a === $b;
}

function loose_equals(mixed $a, mixed $b): bool
{
    return $a == $b;
}

function key_status(array $data, string $key): string
{
    if (!array_key_exists($key, $data)) {
        return 'missing';
    }

    return $data[$key] === null ? 'null' : 'present';
}

function compare(int|float $a, int|float $b): int
{
    return $a <=> $b;
}

<?php

declare(strict_types=1);

// Тема 07: функції та декларації типів — референсний розв'язок.

function format_name(string $first, string $last, ?string $middle = null): string
{
    if ($middle === null) {
        return "{$first} {$last}";
    }

    return "{$first} {$middle} {$last}";
}

function try_parse_int(string $value): ?int
{
    $trimmed = trim($value);

    if (!ctype_digit($trimmed)) {
        return null;
    }

    return (int) $trimmed;
}

function increment_by(int &$value, int $step = 1): void
{
    $value += $step;
}

function next_sequence_value(): int
{
    static $value = 0;

    return ++$value;
}

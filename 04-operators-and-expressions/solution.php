<?php

declare(strict_types=1);

// Тема 04: оператори та вирази — референсний розв'язок.

function integer_divide(int $a, int $b): int
{
    return intdiv($a, $b);
}

function coalesce(mixed $value, mixed $default): mixed
{
    return $value ?? $default;
}

function elvis(mixed $value, mixed $default): mixed
{
    return $value ?: $default;
}

function clamp(int $value, int $min, int $max): int
{
    return max($min, min($max, $value));
}

<?php

declare(strict_types=1);

// Тема 02: типи, константи, strict_types — референсний розв'язок.

const APP_NAME = 'PHP Course';

function type_of(mixed $value): string
{
    return match (gettype($value)) {
        'integer' => 'int',
        'double' => 'float',
        'boolean' => 'bool',
        'NULL' => 'null',
        default => gettype($value),
    };
}

function to_debug_string(mixed $value): string
{
    return var_export($value, true);
}

function sum_strict(int $a, int $b): int
{
    return $a + $b;
}

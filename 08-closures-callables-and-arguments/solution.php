<?php

declare(strict_types=1);

// Тема 08: замикання, callable й аргументи — референсний розв'язок.

function make_multiplier(int $factor): Closure
{
    return fn (int $x): int => $x * $factor;
}

function make_counter(): Closure
{
    $count = 0;

    return function () use (&$count): int {
        return ++$count;
    };
}

function sum_all(int ...$numbers): int
{
    return array_sum($numbers);
}

function string_length_callable(): Closure
{
    return strlen(...);
}

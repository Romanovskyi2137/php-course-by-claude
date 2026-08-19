<?php

declare(strict_types=1);

// Тема 08: замикання, callable й аргументи. Опис завдання — у task.md.

// TODO (вимога 1): Closure через arrow function, що множить на $factor.
function make_multiplier(int $factor): Closure
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): Closure-лічильник через use (&$count), незалежний для кожного виклику.
function make_counter(): Closure
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): сума довільної кількості чисел через variadic.
function sum_all(int ...$numbers): int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): Closure з strlen через first-class callable syntax.
function string_length_callable(): Closure
{
    throw new \RuntimeException('Не реалізовано');
}

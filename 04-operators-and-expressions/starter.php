<?php

declare(strict_types=1);

// Тема 04: оператори та вирази. Опис завдання — у task.md.

// TODO (вимога 1): ціле ділення через intdiv().
function integer_divide(int $a, int $b): int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): null coalescing — $value, якщо не null, інакше $default.
function coalesce(mixed $value, mixed $default): mixed
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): Elvis — $value, якщо truthy, інакше $default.
function elvis(mixed $value, mixed $default): mixed
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): обмеж $value діапазоном [$min, $max].
function clamp(int $value, int $min, int $max): int
{
    throw new \RuntimeException('Не реалізовано');
}

<?php

declare(strict_types=1);

// Тема 07: функції та декларації типів. Опис завдання — у task.md.

// TODO (вимога 1): "First Middle Last" або "First Last", якщо middle немає.
function format_name(string $first, string $last, ?string $middle = null): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): int, якщо $value — ціле число рядком, інакше null. Без винятків.
function try_parse_int(string $value): ?int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): збільш $value на $step за посиланням.
function increment_by(int &$value, int $step = 1): void
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): послідовні числа з 1, через static-змінну.
function next_sequence_value(): int
{
    throw new \RuntimeException('Не реалізовано');
}

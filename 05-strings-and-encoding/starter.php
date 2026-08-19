<?php

declare(strict_types=1);

// Тема 05: рядки та UTF-8. Опис завдання — у task.md.

// TODO (вимога 1): "Привіт, {name}!" через інтерполяцію.
function greet(string $name): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): кількість символів (не байтів!).
function char_count(string $text): int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): верхній регістр, коректно для кирилиці.
function shout(string $text): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): обрізати до $maxLength символів, додати '…' якщо обрізано.
function truncate(string $text, int $maxLength): string
{
    throw new \RuntimeException('Не реалізовано');
}

<?php

declare(strict_types=1);

// Тема 23: дата та час. Опис завдання — у task.md.

// TODO (вимога 1): суворий парсинг 'Y-m-d', InvalidArgumentException на некоректну дату.
function parse_date(string $date): \DateTimeImmutable
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): додати $days робочих днів, пропускаючи вихідні.
function add_business_days(\DateTimeImmutable $date, int $days): \DateTimeImmutable
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): повна кількість прожитих років між датами.
function age_in_years(\DateTimeImmutable $birthDate, \DateTimeImmutable $today): int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): конвертувати момент часу з UTC у вказаний часовий пояс і відформатувати.
function format_in_timezone(\DateTimeImmutable $utc, string $timezone): string
{
    throw new \RuntimeException('Не реалізовано');
}

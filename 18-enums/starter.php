<?php

declare(strict_types=1);

// Тема 18: enums. Опис завдання — у task.md.

// TODO (вимоги 1-2): backed enum Status із трьома кейсами і методом label().
enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): наступний статус у послідовності Draft -> Published -> Archived.
function next_status(Status $status): Status
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): безпечний парсинг рядка в Status, null якщо не відповідає.
function parse_status(string $value): ?Status
{
    throw new \RuntimeException('Не реалізовано');
}

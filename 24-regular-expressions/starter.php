<?php

declare(strict_types=1);

// Тема 24: регулярні вирази. Опис завдання — у task.md.

// TODO (вимога 1): домен після @, null якщо не схоже на email.
function extract_email_domain(string $email): ?string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): схлопнути пробільні символи в один пробіл, обрізати краї.
function normalize_whitespace(string $text): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): розпарсити рядок логу через іменовані групи.
function parse_log_line(string $line): ?array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): розбити текст на слова, коректно для кирилиці (модифікатор u).
function split_ukrainian_words(string $text): array
{
    throw new \RuntimeException('Не реалізовано');
}

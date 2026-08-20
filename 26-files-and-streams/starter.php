<?php

declare(strict_types=1);

// Тема 26: файли та потоки. Опис завдання — у task.md.

// TODO (вимога 1): рядки файлу без символів переносу рядка.
function read_lines(string $path): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 1): дописати рядок у кінець файлу, не перезаписуючи наявний вміст.
function append_log(string $path, string $message): void
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): CSV з заголовками в перший рядок -> масив асоціативних рядків.
function read_csv_as_assoc(string $path): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): порахувати слова, читаючи файл потоково (fopen/fgets).
function count_words_in_stream(string $path): int
{
    throw new \RuntimeException('Не реалізовано');
}

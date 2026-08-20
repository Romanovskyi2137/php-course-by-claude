<?php

declare(strict_types=1);

// Тема 26: файли та потоки — референсний розв'язок.

function read_lines(string $path): array
{
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        throw new \RuntimeException("Не вдалось прочитати {$path}");
    }

    return $lines;
}

function append_log(string $path, string $message): void
{
    file_put_contents($path, $message . PHP_EOL, FILE_APPEND);
}

function read_csv_as_assoc(string $path): array
{
    $handle = fopen($path, 'r');
    if ($handle === false) {
        throw new \RuntimeException("Не вдалось відкрити {$path}");
    }

    $headers = fgetcsv($handle);
    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
        $rows[] = array_combine($headers, $row);
    }

    fclose($handle);

    return $rows;
}

function count_words_in_stream(string $path): int
{
    $handle = fopen($path, 'r');
    if ($handle === false) {
        throw new \RuntimeException("Не вдалось відкрити {$path}");
    }

    $count = 0;
    while (($line = fgets($handle)) !== false) {
        $count += str_word_count($line);
    }

    fclose($handle);

    return $count;
}

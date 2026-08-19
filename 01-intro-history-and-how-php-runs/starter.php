<?php

declare(strict_types=1);

// Тема 01: інспектор оточення PHP. Опис завдання — у task.md.

// TODO (вимога 1): поверни major-версію PHP, обчислену з PHP_VERSION_ID.
function php_major_version(): int
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): поверни true, якщо поточна версія PHP >= $version.
function is_at_least(string $version): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): поверни назву поточного SAPI.
function current_sapi(): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): поверни багаторядковий звіт у форматі з task.md.
function format_environment_report(): string
{
    throw new \RuntimeException('Не реалізовано');
}

if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    echo format_environment_report() . "\n";
}

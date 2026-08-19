<?php

declare(strict_types=1);

// Тема 01: інспектор оточення PHP — референсний розв'язок.

function php_major_version(): int
{
    // PHP_VERSION_ID для 8.3.6 = 80306 = 8*10000 + 3*100 + 6.
    // intdiv(), а не /, щоб гарантовано отримати int, а не float.
    return intdiv(PHP_VERSION_ID, 10000);
}

function is_at_least(string $version): bool
{
    return version_compare(PHP_VERSION, $version, '>=');
}

function current_sapi(): string
{
    return PHP_SAPI;
}

function format_environment_report(): string
{
    return implode("\n", [
        'PHP version: ' . PHP_VERSION,
        'SAPI: ' . PHP_SAPI,
        'OS family: ' . PHP_OS_FAMILY,
        'Loaded extensions: ' . count(get_loaded_extensions()),
    ]);
}

// Альтернативний підхід до php_major_version(): парсинг PHP_VERSION рядком.
// Працює, але крихкіший (залежить від формату рядка) і саме тому в задачі
// заборонений — PHP_VERSION_ID існує спеціально, щоб цього уникнути:
//
//     function php_major_version(): int
//     {
//         return (int) explode('.', PHP_VERSION)[0];
//     }

if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    echo format_environment_report() . "\n";
}

<?php

declare(strict_types=1);

// Тема 01: інспектор оточення PHP. Опис завдання — у task.md.

// TODO (вимога 1): поверни major-версію PHP, обчислену з PHP_VERSION_ID.
function php_major_version(): int
{
    return (int) (PHP_VERSION_ID / 10000);
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): поверни true, якщо поточна версія PHP >= $version.
function is_at_least(string $version): bool
{
    if (PHP_VERSION >= $version) {
        return true;
    } else {
        return false;
    }
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): поверни назву поточного SAPI.
function current_sapi(): string
{
    return PHP_SAPI;
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): поверни багаторядковий звіт у форматі з task.md.
function format_environment_report(): string
{
    $output = 
        "PHP version: " . PHP_VERSION . "\n" .
        "SAPI: " . PHP_SAPI . "\n" .
        "OS family: " . PHP_OS_FAMILY . "\n" .
        "Loaded extensions: " . count(get_loaded_extensions())
    ;
    echo $output;
    return $output;    
    throw new \RuntimeException('Не реалізовано');
}

// PHP version: 8.3.6
// SAPI: cli
// OS family: Linux
// Loaded extensions: 42


if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    echo format_environment_report() . "\n";
}

<?php

declare(strict_types=1);

namespace App;

// Тема 46: автентифікація API, помилки та логування. Опис завдання — у task.md.

interface Logger
{
    public function info(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}

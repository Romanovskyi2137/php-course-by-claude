<?php

declare(strict_types=1);

namespace App;

// Тема 46: автентифікація API, помилки та логування — референсний розв'язок.

final class ArrayLogger implements Logger
{
    private array $entries = [];

    public function info(string $message, array $context = []): void
    {
        $this->entries[] = ['level' => 'info', 'message' => $message, 'context' => $context];
    }

    public function error(string $message, array $context = []): void
    {
        $this->entries[] = ['level' => 'error', 'message' => $message, 'context' => $context];
    }

    public function entries(): array
    {
        return $this->entries;
    }
}

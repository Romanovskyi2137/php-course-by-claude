<?php

declare(strict_types=1);

namespace App;

// Тема 45: REST API на голому PHP. Опис завдання — у task.md.

// TODO (вимога 3): узгодження вмісту за заголовком Accept.
function negotiate_content_type(string $acceptHeader, array $available): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): побудувати JSON-відповідь як дані.
function json_response(mixed $data, int $status = 200): array
{
    throw new \RuntimeException('Не реалізовано');
}

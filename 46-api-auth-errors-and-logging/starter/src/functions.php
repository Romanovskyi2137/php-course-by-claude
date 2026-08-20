<?php

declare(strict_types=1);

namespace App;

// Тема 46: автентифікація API, помилки та логування. Опис завдання — у task.md.

// TODO (вимога 1): дістати Bearer-токен із заголовків.
function extract_bearer_token(array $server): ?string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): перевірити токен, повернути userId або null.
function authenticate(array $server, array $validTokens): ?string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): уніфікована відповідь-помилка API.
function error_response(string $message, int $status): array
{
    throw new \RuntimeException('Не реалізовано');
}

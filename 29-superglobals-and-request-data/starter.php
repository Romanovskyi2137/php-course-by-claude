<?php

declare(strict_types=1);

// Тема 29: суперглобали та дані запиту. Опис завдання — у task.md.

// TODO (вимога 1): IP клієнта з урахуванням X-Forwarded-For.
function client_ip(array $server): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): чи є Content-Type запиту application/json.
function is_json_request(array $server): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): розпарсити сире JSON-тіло, JsonException на помилку.
function parse_json_body(string $rawBody): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): білий список дозволених GET-параметрів, з trim().
function sanitize_get_params(array $get, array $allowedKeys): array
{
    throw new \RuntimeException('Не реалізовано');
}

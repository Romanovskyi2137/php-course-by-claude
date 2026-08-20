<?php

declare(strict_types=1);

// Тема 29: суперглобали та дані запиту — референсний розв'язок.

function client_ip(array $server): string
{
    if (isset($server['HTTP_X_FORWARDED_FOR'])) {
        [$first] = explode(',', $server['HTTP_X_FORWARDED_FOR']);

        return trim($first);
    }

    return $server['REMOTE_ADDR'] ?? 'unknown';
}

function is_json_request(array $server): bool
{
    return str_contains($server['CONTENT_TYPE'] ?? '', 'application/json');
}

function parse_json_body(string $rawBody): array
{
    return json_decode($rawBody, associative: true, flags: JSON_THROW_ON_ERROR);
}

function sanitize_get_params(array $get, array $allowedKeys): array
{
    $result = [];
    foreach ($allowedKeys as $key) {
        if (array_key_exists($key, $get)) {
            $result[$key] = trim((string) $get[$key]);
        }
    }

    return $result;
}

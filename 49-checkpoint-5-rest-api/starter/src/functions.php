<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: auth/response-хелпери з теми 46. Готовий код — не редагуй.

function extract_bearer_token(array $server): ?string
{
    $header = $server['HTTP_AUTHORIZATION'] ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        return null;
    }

    return substr($header, strlen('Bearer '));
}

function authenticate(array $server, array $validTokens): ?string
{
    $token = extract_bearer_token($server);

    return $token !== null ? ($validTokens[$token] ?? null) : null;
}

function json_response(mixed $data, int $status = 200): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
    ];
}

function error_response(string $message, int $status): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
    ];
}

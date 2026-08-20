<?php

declare(strict_types=1);

namespace App;

// Тема 46: автентифікація API, помилки та логування — референсний розв'язок.

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

function error_response(string $message, int $status): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
    ];
}

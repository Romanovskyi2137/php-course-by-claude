<?php

declare(strict_types=1);

// Тема 28: життєвий цикл веб-запиту — референсний розв'язок.

function parse_request_line(string $requestLine): array
{
    [$method, $target, $protocol] = explode(' ', $requestLine);
    [$path, $query] = str_contains($target, '?')
        ? explode('?', $target, 2)
        : [$target, null];

    return [
        'method' => $method,
        'path' => $path,
        'query' => $query,
        'protocolVersion' => str_replace('HTTP/', '', $protocol),
    ];
}

function reason_phrase(int $statusCode): string
{
    $phrases = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];

    if (!isset($phrases[$statusCode])) {
        throw new \InvalidArgumentException("Невідомий код статусу: {$statusCode}");
    }

    return $phrases[$statusCode];
}

function status_class(int $statusCode): string
{
    return match (true) {
        $statusCode >= 100 && $statusCode < 200 => 'informational',
        $statusCode >= 200 && $statusCode < 300 => 'success',
        $statusCode >= 300 && $statusCode < 400 => 'redirect',
        $statusCode >= 400 && $statusCode < 500 => 'client_error',
        $statusCode >= 500 && $statusCode < 600 => 'server_error',
        default => throw new \InvalidArgumentException("Некоректний код статусу: {$statusCode}"),
    };
}

function build_query_string(array $params): string
{
    return http_build_query($params);
}

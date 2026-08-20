<?php

declare(strict_types=1);

namespace App;

// Тема 45: REST API на голому PHP — референсний розв'язок.

function negotiate_content_type(string $acceptHeader, array $available): string
{
    foreach (array_map('trim', explode(',', $acceptHeader)) as $entry) {
        $type = explode(';', $entry)[0];

        if ($type === '*/*') {
            return $available[0];
        }
        if (in_array($type, $available, true)) {
            return $type;
        }
    }

    return $available[0];
}

function json_response(mixed $data, int $status = 200): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data, JSON_THROW_ON_ERROR),
    ];
}

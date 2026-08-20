<?php

declare(strict_types=1);

// Тема 33: основи веб-безпеки — референсний розв'язок.

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_csrf_token(): string
{
    return bin2hex(random_bytes(32));
}

function verify_csrf_token(?string $expected, ?string $actual): bool
{
    if ($expected === null || $actual === null) {
        return false;
    }

    return hash_equals($expected, $actual);
}

function filter_allowed_fields(array $input, array $allowedFields): array
{
    return array_intersect_key($input, array_flip($allowedFields));
}

function content_security_policy_header(array $directives): string
{
    $parts = [];
    foreach ($directives as $name => $value) {
        $parts[] = "{$name} {$value}";
    }

    return implode('; ', $parts);
}

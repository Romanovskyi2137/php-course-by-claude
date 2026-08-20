<?php

declare(strict_types=1);

// Тема 33: основи веб-безпеки. Опис завдання — у task.md.

// TODO (вимога 1): екранувати рядок для безпечного виводу в HTML.
function escape_html(string $value): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): згенерувати випадковий CSRF-токен.
function generate_csrf_token(): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): порівняти CSRF-токени за постійний час, null-безпечно.
function verify_csrf_token(?string $expected, ?string $actual): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): білий список дозволених полів (mass assignment guard).
function filter_allowed_fields(array $input, array $allowedFields): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 5): побудувати значення заголовка Content-Security-Policy.
function content_security_policy_header(array $directives): string
{
    throw new \RuntimeException('Не реалізовано');
}

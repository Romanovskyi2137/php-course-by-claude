<?php

declare(strict_types=1);

// Тема 28: життєвий цикл веб-запиту. Опис завдання — у task.md.

// TODO (вимога 1): розібрати рядок запиту на method/path/query/protocolVersion.
function parse_request_line(string $requestLine): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): стандартна фраза-пояснення для коду статусу.
function reason_phrase(int $statusCode): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): клас коду статусу (informational/success/redirect/client_error/server_error).
function status_class(int $statusCode): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): побудувати query-рядок з асоціативного масиву.
function build_query_string(array $params): string
{
    throw new \RuntimeException('Не реалізовано');
}

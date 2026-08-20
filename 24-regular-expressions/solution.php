<?php

declare(strict_types=1);

// Тема 24: регулярні вирази — референсний розв'язок.

function extract_email_domain(string $email): ?string
{
    if (preg_match('/@([a-zA-Z0-9.\-]+)$/', $email, $matches) === 1) {
        return $matches[1];
    }

    return null;
}

function normalize_whitespace(string $text): string
{
    return trim(preg_replace('/\s+/u', ' ', $text));
}

function parse_log_line(string $line): ?array
{
    $pattern = '/^\[(?<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(?<level>[A-Z]+):\s+(?<message>.+)$/u';
    if (preg_match($pattern, $line, $m) !== 1) {
        return null;
    }

    return ['date' => $m['date'], 'level' => $m['level'], 'message' => $m['message']];
}

function split_ukrainian_words(string $text): array
{
    return preg_split('/[^\p{L}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
}

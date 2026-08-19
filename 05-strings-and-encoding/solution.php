<?php

declare(strict_types=1);

// Тема 05: рядки та UTF-8 — референсний розв'язок.

function greet(string $name): string
{
    return "Привіт, {$name}!";
}

function char_count(string $text): int
{
    return mb_strlen($text);
}

function shout(string $text): string
{
    return mb_strtoupper($text);
}

function truncate(string $text, int $maxLength): string
{
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }

    return mb_substr($text, 0, $maxLength) . '…';
}

<?php

declare(strict_types=1);

// Тема 10: функції для роботи з масивами — референсний розв'язок.

function active_usernames(array $users): array
{
    $active = array_filter($users, fn (array $u): bool => $u['active']);
    $names = array_map(fn (array $u): string => $u['name'], $active);

    return array_values($names);
}

function total_price(array $items): float
{
    return array_reduce(
        $items,
        fn (float $carry, array $item): float => $carry + $item['price'] * $item['qty'],
        0.0
    );
}

function sorted_by_score_desc(array $players): array
{
    $sorted = $players;
    usort($sorted, fn (array $a, array $b): int => $b['score'] <=> $a['score']);

    return $sorted;
}

function index_by_id(array $records): array
{
    return array_column($records, null, 'id');
}

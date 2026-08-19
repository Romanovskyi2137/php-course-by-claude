<?php

declare(strict_types=1);

// Тема 09: масиви та деструктуризація — референсний розв'язок.

function swap(array $pair): array
{
    [$a, $b] = $pair;

    return [$b, $a];
}

function merge_configs(array $defaults, array $overrides): array
{
    return [...$defaults, ...$overrides];
}

function append(array $items, mixed $value): array
{
    $items[] = $value;

    return $items;
}

function pluck(array $records, string $key): array
{
    $result = [];

    foreach ($records as $record) {
        $result[] = $record[$key];
    }

    return $result;
}

<?php

declare(strict_types=1);

namespace App;

// Тема 48: деплой та продуктивність. Опис завдання — у task.md.

// TODO (вимога 1): health-check ендпоінт як чиста функція.
function health_check(array $checks): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): hit rate OPcache з сирих лічильників.
function opcache_hit_rate(int $hits, int $misses): float
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): читабельний uptime.
function format_uptime(int $seconds): string
{
    throw new \RuntimeException('Не реалізовано');
}

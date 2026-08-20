<?php

declare(strict_types=1);

namespace App;

// Тема 48: деплой та продуктивність — референсний розв'язок.

function health_check(array $checks): array
{
    $results = [];
    foreach ($checks as $name => $check) {
        $results[$name] = $check() ? 'ok' : 'fail';
    }

    $allOk = !in_array('fail', $results, true);

    return ['status' => $allOk ? 'ok' : 'degraded', 'checks' => $results];
}

function opcache_hit_rate(int $hits, int $misses): float
{
    $total = $hits + $misses;
    if ($total === 0) {
        return 0.0;
    }

    return round($hits / $total * 100, 2);
}

function format_uptime(int $seconds): string
{
    $days = intdiv($seconds, 86400);
    $hours = intdiv($seconds % 86400, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $secs = $seconds % 60;

    if ($days > 0) {
        return "{$days}d {$hours}h";
    }
    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    }
    if ($minutes > 0) {
        return "{$minutes}m {$secs}s";
    }

    return "{$secs}s";
}

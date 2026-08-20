<?php

declare(strict_types=1);

namespace App;

// Тема 45: REST API на голому PHP. Опис завдання — у task.md.

// TODO (вимоги 1-2): роутер з підтримкою параметрів шляху й 404/405.
final class Router
{
    private array $routes = [];

    public function register(string $method, string $pattern, callable $handler): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function dispatch(string $method, string $path): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: роутер з теми 45. Готовий код — не редагуй.

final class Router
{
    private array $routes = [];

    public function register(string $method, string $pattern, callable $handler): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?<$1>[^/]+)', $pattern);
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $path): array
    {
        $pathMatchedAnyMethod = false;

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches) !== 1) {
                continue;
            }
            if ($route['method'] !== strtoupper($method)) {
                $pathMatchedAnyMethod = true;
                continue;
            }
            $params = array_filter($matches, static fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

            return ($route['handler'])($params);
        }

        return $pathMatchedAnyMethod
            ? ['status' => 405, 'body' => ['error' => 'Method Not Allowed']]
            : ['status' => 404, 'body' => ['error' => 'Not Found']];
    }
}

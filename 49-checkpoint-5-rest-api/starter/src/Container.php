<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок. Опис вимог — у task.md.

// TODO (вимога 3): мінімальний DI-контейнер з теми 41.
final class Container
{
    public function set(string $id, \Closure $factory): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function get(string $id): mixed
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function has(string $id): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

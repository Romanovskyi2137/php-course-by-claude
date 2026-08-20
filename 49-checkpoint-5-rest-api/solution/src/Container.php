<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок — референсний розв'язок.

final class Container
{
    private array $factories = [];
    private array $instances = [];

    public function set(string $id, \Closure $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (!array_key_exists($id, $this->instances)) {
            if (!isset($this->factories[$id])) {
                throw new \OutOfBoundsException("Сервіс не зареєстровано: {$id}");
            }
            $this->instances[$id] = ($this->factories[$id])($this);
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}

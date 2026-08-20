<?php

declare(strict_types=1);

// Тема 41: Dependency Injection та контейнери — референсний розв'язок.

interface Logger
{
    public function log(string $message): void;
}

final class ArrayLogger implements Logger
{
    private array $messages = [];

    public function log(string $message): void
    {
        $this->messages[] = $message;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}

final class OrderService
{
    public function __construct(private Logger $logger)
    {
    }

    public function placeOrder(string $item): void
    {
        $this->logger->log("Order placed: {$item}");
    }
}

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

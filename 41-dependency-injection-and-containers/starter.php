<?php

declare(strict_types=1);

// Тема 41: Dependency Injection та контейнери. Опис завдання — у task.md.

// TODO (вимога 1): Logger-інтерфейс і логер у пам'яті.
interface Logger
{
    public function log(string $message): void;
}

class ArrayLogger implements Logger
{
    public function log(string $message): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function messages(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 2): DI через конструктор, не Service Locator.
class OrderService
{
    public function __construct(Logger $logger)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function placeOrder(string $item): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): мінімальний DI-контейнер.
class Container
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

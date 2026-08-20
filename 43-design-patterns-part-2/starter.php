<?php

declare(strict_types=1);

// Тема 43: патерни проєктування, частина 2. Опис завдання — у task.md.

// TODO (вимога 1): Strategy.
interface DiscountStrategy
{
    public function apply(float $price): float;
}

class NoDiscount implements DiscountStrategy
{
    public function apply(float $price): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class PercentageDiscount implements DiscountStrategy
{
    public function __construct(float $percent)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function apply(float $price): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class PriceCalculator
{
    public function __construct(DiscountStrategy $strategy)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function finalPrice(float $price): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 2): Observer.
interface Observer
{
    public function update(string $event): void;
}

class EventEmitter
{
    public function subscribe(Observer $observer): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function notify(string $event): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class LoggingObserver implements Observer
{
    public function update(string $event): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function log(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): Repository.
interface UserRepository
{
    public function save(array $user): void;

    public function findByEmail(string $email): ?array;
}

class InMemoryUserRepository implements UserRepository
{
    public function save(array $user): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function findByEmail(string $email): ?array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): Service layer.
class RegistrationService
{
    public function __construct(UserRepository $repository)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function register(string $email, string $name): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

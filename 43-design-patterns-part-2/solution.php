<?php

declare(strict_types=1);

// Тема 43: патерни проєктування, частина 2 — референсний розв'язок.

interface DiscountStrategy
{
    public function apply(float $price): float;
}

final class NoDiscount implements DiscountStrategy
{
    public function apply(float $price): float
    {
        return $price;
    }
}

final class PercentageDiscount implements DiscountStrategy
{
    public function __construct(private float $percent)
    {
    }

    public function apply(float $price): float
    {
        return $price * (1 - $this->percent / 100);
    }
}

final class PriceCalculator
{
    public function __construct(private DiscountStrategy $strategy)
    {
    }

    public function finalPrice(float $price): float
    {
        return $this->strategy->apply($price);
    }
}

interface Observer
{
    public function update(string $event): void;
}

final class EventEmitter
{
    private array $observers = [];

    public function subscribe(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notify(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($event);
        }
    }
}

final class LoggingObserver implements Observer
{
    private array $log = [];

    public function update(string $event): void
    {
        $this->log[] = $event;
    }

    public function log(): array
    {
        return $this->log;
    }
}

interface UserRepository
{
    public function save(array $user): void;

    public function findByEmail(string $email): ?array;
}

final class InMemoryUserRepository implements UserRepository
{
    private array $users = [];

    public function save(array $user): void
    {
        $this->users[$user['email']] = $user;
    }

    public function findByEmail(string $email): ?array
    {
        return $this->users[$email] ?? null;
    }
}

final class RegistrationService
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function register(string $email, string $name): bool
    {
        if ($this->repository->findByEmail($email) !== null) {
            return false;
        }

        $this->repository->save(['email' => $email, 'name' => $name]);

        return true;
    }
}

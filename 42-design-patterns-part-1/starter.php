<?php

declare(strict_types=1);

// Тема 42: патерни проєктування, частина 1. Опис завдання — у task.md.

// TODO (вимога 1): Factory.
interface Shape
{
    public function area(): float;
}

class Circle implements Shape
{
    public function __construct(float $radius)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function area(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class Rectangle implements Shape
{
    public function __construct(float $width, float $height)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function area(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class ShapeFactory
{
    public static function create(string $type, float ...$dimensions): Shape
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 2): Builder.
class QueryBuilder
{
    public function select(string ...$columns): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function from(string $table): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function where(string $condition): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function build(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): Singleton.
class AppConfig
{
    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function set(string $key, mixed $value): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function get(string $key): mixed
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): Decorator.
interface Coffee
{
    public function cost(): float;
    public function description(): string;
}

class SimpleCoffee implements Coffee
{
    public function cost(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function description(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

abstract class CoffeeDecorator implements Coffee
{
    public function __construct(protected Coffee $coffee)
    {
    }
}

class MilkDecorator extends CoffeeDecorator
{
    public function cost(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function description(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

class SugarDecorator extends CoffeeDecorator
{
    public function cost(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function description(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

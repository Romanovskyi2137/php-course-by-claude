<?php

declare(strict_types=1);

// Тема 42: патерни проєктування, частина 1 — референсний розв'язок.

interface Shape
{
    public function area(): float;
}

final class Circle implements Shape
{
    public function __construct(private float $radius)
    {
    }

    public function area(): float
    {
        return M_PI * $this->radius ** 2;
    }
}

final class Rectangle implements Shape
{
    public function __construct(private float $width, private float $height)
    {
    }

    public function area(): float
    {
        return $this->width * $this->height;
    }
}

final class ShapeFactory
{
    public static function create(string $type, float ...$dimensions): Shape
    {
        return match ($type) {
            'circle' => new Circle($dimensions[0]),
            'rectangle' => new Rectangle($dimensions[0], $dimensions[1]),
            default => throw new \InvalidArgumentException("Невідома форма: {$type}"),
        };
    }
}

final class QueryBuilder
{
    private array $columns = ['*'];
    private string $table = '';
    private array $conditions = [];

    public function select(string ...$columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function where(string $condition): self
    {
        $this->conditions[] = $condition;

        return $this;
    }

    public function build(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;
        if ($this->conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->conditions);
        }

        return $sql;
    }
}

final class AppConfig
{
    private static ?self $instance = null;
    private array $values = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }
}

interface Coffee
{
    public function cost(): float;
    public function description(): string;
}

final class SimpleCoffee implements Coffee
{
    public function cost(): float
    {
        return 30.0;
    }

    public function description(): string
    {
        return 'Кава';
    }
}

abstract class CoffeeDecorator implements Coffee
{
    public function __construct(protected Coffee $coffee)
    {
    }
}

final class MilkDecorator extends CoffeeDecorator
{
    public function cost(): float
    {
        return $this->coffee->cost() + 10.0;
    }

    public function description(): string
    {
        return $this->coffee->description() . ' + молоко';
    }
}

final class SugarDecorator extends CoffeeDecorator
{
    public function cost(): float
    {
        return $this->coffee->cost() + 5.0;
    }

    public function description(): string
    {
        return $this->coffee->description() . ' + цукор';
    }
}

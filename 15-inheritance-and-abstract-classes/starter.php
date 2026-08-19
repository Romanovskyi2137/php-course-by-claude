<?php

declare(strict_types=1);

// Тема 15: успадкування та абстрактні класи. Опис завдання — у task.md.

// TODO (вимога 1): абстрактний area(), звичайний describe() через $this->area().
abstract class Shape
{
    abstract public function area(): float;

    public function describe(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 2): area() = M_PI * radius^2.
class Circle extends Shape
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

// TODO (вимога 3): area() = side^2.
class Square extends Shape
{
    public function __construct(float $side)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function area(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): Animal з describe().
class Animal
{
    public function __construct(protected string $name)
    {
    }

    public function describe(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): Dog викликає parent::__construct() і parent::describe().
class Dog extends Animal
{
    public function __construct(string $name, private string $breed)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function describe(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

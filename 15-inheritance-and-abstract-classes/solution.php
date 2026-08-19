<?php

declare(strict_types=1);

// Тема 15: успадкування та абстрактні класи — референсний розв'язок.

abstract class Shape
{
    abstract public function area(): float;

    public function describe(): string
    {
        return sprintf('Площа: %.2f', $this->area());
    }
}

class Circle extends Shape
{
    public function __construct(private float $radius)
    {
    }

    public function area(): float
    {
        return M_PI * $this->radius ** 2;
    }
}

class Square extends Shape
{
    public function __construct(private float $side)
    {
    }

    public function area(): float
    {
        return $this->side ** 2;
    }
}

class Animal
{
    public function __construct(protected string $name)
    {
    }

    public function describe(): string
    {
        return "Тварина на ім'я {$this->name}";
    }
}

class Dog extends Animal
{
    public function __construct(string $name, private string $breed)
    {
        parent::__construct($name);
    }

    public function describe(): string
    {
        return parent::describe() . ", порода {$this->breed}";
    }
}

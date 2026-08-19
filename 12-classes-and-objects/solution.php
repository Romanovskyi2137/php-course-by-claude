<?php

declare(strict_types=1);

// Тема 12: класи та об'єкти — референсний розв'язок.

class Point3D
{
    private float $x;
    private float $y;
    private float $z;

    public function __construct(float $x, float $y, float $z)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function z(): float
    {
        return $this->z;
    }

    public function length(): float
    {
        return sqrt($this->x ** 2 + $this->y ** 2 + $this->z ** 2);
    }

    public function withX(float $x): void
    {
        $this->x = $x;
    }
}

class Address
{
    public string $city;

    public function __construct(string $city)
    {
        $this->city = $city;
    }
}

class Profile
{
    public ?Address $address = null;
}

function profile_city(Profile $profile): ?string
{
    return $profile->address?->city;
}

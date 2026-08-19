<?php

declare(strict_types=1);

// Тема 12: класи та об'єкти. Опис завдання — у task.md.

// TODO (вимоги 1-3): клас Point3D з геттерами, length() і withX().
class Point3D
{
    public function __construct(float $x, float $y, float $z)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function x(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function y(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function z(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function length(): float
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function withX(float $x): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): клас Address з публічною властивістю city.
class Address
{
    public function __construct(string $city)
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): клас Profile з публічною ?Address $address = null.
class Profile
{
}

// TODO (вимога 4): місто профілю через nullsafe-оператор.
function profile_city(Profile $profile): ?string
{
    throw new \RuntimeException('Не реалізовано');
}

<?php

declare(strict_types=1);

// Тема 13: promotion, readonly, клонування. Опис завдання — у task.md.

// TODO (вимоги 1-3): readonly-властивості через promotion, add() повертає новий обʼєкт.
class Vector2D
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function add(Vector2D $other): Vector2D
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): властивість name через promotion.
class Tag
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): властивість tag через promotion + __clone() для глибокого клонування.
class Box
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function __clone(): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

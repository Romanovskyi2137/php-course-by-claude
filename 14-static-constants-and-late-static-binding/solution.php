<?php

declare(strict_types=1);

// Тема 14: static, константи класу, LSB — референсний розв'язок.

class IdGenerator
{
    private static int $value = 0;

    public static function next(): int
    {
        return ++self::$value;
    }

    public static function current(): int
    {
        return self::$value;
    }

    public static function reset(): void
    {
        self::$value = 0;
    }
}

class Model
{
    public const string TABLE = 'models';

    public static function tableViaSelf(): string
    {
        return self::TABLE;
    }

    public static function tableViaStatic(): string
    {
        return static::TABLE;
    }

    public static function create(): static
    {
        return new static();
    }
}

class Product extends Model
{
    public const string TABLE = 'products';
}

class Widget extends Model
{
}

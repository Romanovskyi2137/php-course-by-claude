<?php

declare(strict_types=1);

// Тема 14: static, константи класу, LSB. Опис завдання — у task.md.

// TODO (вимога 1): статичний лічильник next()/current()/reset().
class IdGenerator
{
    public static function next(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function current(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function reset(): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимоги 2-3): self:: vs static::, і new static() у create().
class Model
{
    public const string TABLE = 'models';

    public static function tableViaSelf(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function tableViaStatic(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function create(): static
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// Готові класи — не потребують змін, використовуються для перевірки LSB.
class Product extends Model
{
    public const string TABLE = 'products';
}

class Widget extends Model
{
}

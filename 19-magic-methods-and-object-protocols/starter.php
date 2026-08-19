<?php

declare(strict_types=1);

// Тема 19: магічні методи та об'єктні протоколи. Опис завдання — у task.md.

// TODO (вимога 1): __get/__set/__isset над внутрішнім масивом.
class DynamicBag
{
}

// TODO (вимога 2): __toString() у форматі '12.50'.
class Money implements \Stringable
{
    public function __construct(int $cents)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function __toString(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): ArrayAccess + Countable над внутрішнім масивом.
class Config implements \ArrayAccess, \Countable
{
    public function __construct(array $data)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function offsetExists(mixed $offset): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function offsetGet(mixed $offset): mixed
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function count(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

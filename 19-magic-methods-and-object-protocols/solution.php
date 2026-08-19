<?php

declare(strict_types=1);

// Тема 19: магічні методи та об'єктні протоколи — референсний розв'язок.

class DynamicBag
{
    private array $data = [];

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
}

class Money implements \Stringable
{
    public function __construct(private int $cents)
    {
    }

    public function __toString(): string
    {
        return number_format($this->cents / 100, 2);
    }
}

class Config implements \ArrayAccess, \Countable
{
    public function __construct(private array $data)
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function count(): int
    {
        return count($this->data);
    }
}

<?php

declare(strict_types=1);

// Тема 13: promotion, readonly, клонування — референсний розв'язок.

class Vector2D
{
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {
    }

    public function add(Vector2D $other): Vector2D
    {
        return new self($this->x + $other->x, $this->y + $other->y);
    }
}

class Tag
{
    public function __construct(public string $name)
    {
    }
}

class Box
{
    public function __construct(public Tag $tag)
    {
    }

    public function __clone(): void
    {
        $this->tag = clone $this->tag;
    }
}

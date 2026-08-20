<?php

declare(strict_types=1);

// Тема 22: ітератори, генератори та SPL. Опис завдання — у task.md.

// TODO (вимога 1): генератор, що видає числа від $from до $to включно.
function count_up(int $from, int $to): \Generator
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): генератор, що видає значення з вкладеного масиву "плоско" через yield from.
function flatten(array $nested): \Generator
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): ручна реалізація Iterator над масивом треків.
class Playlist implements \Iterator, \Countable
{
    public function __construct(array $tracks)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function current(): string
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function key(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function next(): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function rewind(): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function valid(): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function count(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 4): обмежена історія на основі SplQueue.
class RecentlyPlayed
{
    public function __construct(int $capacity)
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function push(string $track): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toArray(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

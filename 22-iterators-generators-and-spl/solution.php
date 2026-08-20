<?php

declare(strict_types=1);

// Тема 22: ітератори, генератори та SPL — референсний розв'язок.

function count_up(int $from, int $to): \Generator
{
    for ($i = $from; $i <= $to; $i++) {
        yield $i;
    }
}

function flatten(array $nested): \Generator
{
    foreach ($nested as $item) {
        if (is_array($item)) {
            yield from flatten($item);
        } else {
            yield $item;
        }
    }
}

class Playlist implements \Iterator, \Countable
{
    private int $position = 0;

    public function __construct(private array $tracks)
    {
    }

    public function current(): string
    {
        return $this->tracks[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->tracks[$this->position]);
    }

    public function count(): int
    {
        return count($this->tracks);
    }
}

class RecentlyPlayed
{
    private \SplQueue $queue;

    public function __construct(private int $capacity)
    {
        $this->queue = new \SplQueue();
    }

    public function push(string $track): void
    {
        $this->queue->enqueue($track);
        if ($this->queue->count() > $this->capacity) {
            $this->queue->dequeue();
        }
    }

    public function toArray(): array
    {
        return iterator_to_array($this->queue, false);
    }
}

<?php

declare(strict_types=1);

// Тема 17: traits — референсний розв'язок.

trait Loggable
{
    private array $log = [];

    public function log(string $message): void
    {
        $this->log[] = $message;
    }

    public function logs(): array
    {
        return $this->log;
    }
}

trait Timestamped
{
    private ?string $createdAt = null;

    public function touch(): void
    {
        $this->createdAt = date('Y-m-d');
    }

    public function createdAt(): ?string
    {
        return $this->createdAt;
    }
}

class Article
{
    use Loggable, Timestamped;
}

trait Greets
{
    public function hello(): string
    {
        return 'Hello from Greets';
    }
}

trait Speaks
{
    public function hello(): string
    {
        return 'Hello from Speaks';
    }
}

class Robot
{
    use Greets, Speaks {
        Greets::hello insteadof Speaks;
        Speaks::hello as speaksHello;
    }
}

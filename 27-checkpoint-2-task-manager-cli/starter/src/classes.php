<?php

declare(strict_types=1);

// Checkpoint 2: task manager CLI. Опис вимог — у task.md.

// TODO (вимога 1): backed enum зі статусами задачі.
enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
}

// TODO (вимога 2): readonly DTO задачі.
final class Task
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function isOverdue(\DateTimeImmutable $now): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function withStatus(TaskStatus $status): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toArray(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимоги 3-5): сховище задач у пам'яті з JSON-персистенцією.
final class TaskRepository
{
    public function __construct(array $tasks = [])
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function add(Task $task): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function all(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function findById(int $id): ?Task
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function byStatus(TaskStatus $status): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function count(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function overdue(\DateTimeImmutable $now): \Generator
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function fromJsonFile(string $path): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toJsonFile(string $path): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

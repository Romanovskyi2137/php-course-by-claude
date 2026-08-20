<?php

declare(strict_types=1);

// Checkpoint 2: task manager CLI — референсний розв'язок.

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
}

final class Task
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly TaskStatus $status,
        public readonly \DateTimeImmutable $dueDate,
        public readonly int $priority = 3,
    ) {
    }

    public function isOverdue(\DateTimeImmutable $now): bool
    {
        return $this->status !== TaskStatus::Done && $this->dueDate < $now;
    }

    public function withStatus(TaskStatus $status): self
    {
        return new self($this->id, $this->title, $status, $this->dueDate, $this->priority);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status->value,
            'dueDate' => $this->dueDate->format('Y-m-d'),
            'priority' => $this->priority,
        ];
    }
}

final class TaskRepository
{
    public function __construct(private array $tasks = [])
    {
    }

    public function add(Task $task): void
    {
        $this->tasks[] = $task;
    }

    public function all(): array
    {
        return $this->tasks;
    }

    public function findById(int $id): ?Task
    {
        foreach ($this->tasks as $task) {
            if ($task->id === $id) {
                return $task;
            }
        }

        return null;
    }

    public function byStatus(TaskStatus $status): array
    {
        return array_values(array_filter(
            $this->tasks,
            static fn (Task $task): bool => $task->status === $status
        ));
    }

    public function count(): int
    {
        return count($this->tasks);
    }

    public function overdue(\DateTimeImmutable $now): \Generator
    {
        foreach ($this->tasks as $task) {
            if ($task->isOverdue($now)) {
                yield $task;
            }
        }
    }

    public static function fromJsonFile(string $path): self
    {
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("Не вдалось прочитати {$path}");
        }

        $rows = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);
        $tasks = array_map(
            static fn (array $row): Task => new Task(
                id: $row['id'],
                title: $row['title'],
                status: TaskStatus::from($row['status']),
                dueDate: new \DateTimeImmutable($row['dueDate']),
                priority: $row['priority'] ?? 3,
            ),
            $rows
        );

        return new self($tasks);
    }

    public function toJsonFile(string $path): void
    {
        $rows = array_map(static fn (Task $task): array => $task->toArray(), $this->tasks);
        file_put_contents(
            $path,
            json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}

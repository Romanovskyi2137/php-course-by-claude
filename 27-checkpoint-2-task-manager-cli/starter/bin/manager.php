<?php

declare(strict_types=1);

// Checkpoint 2: CLI-обгортка. Готовий код — не потрібно редагувати.
// Логіка живе в ../src/classes.php, тут лише розбір команди й вивід.

require __DIR__ . '/../src/classes.php';

$repository = TaskRepository::fromJsonFile(__DIR__ . '/../../data/tasks.json');

function printTask(Task $task): void
{
    printf(
        "#%d [%s] %s (due %s, priority %d)\n",
        $task->id,
        $task->status->value,
        $task->title,
        $task->dueDate->format('Y-m-d'),
        $task->priority
    );
}

$command = $argv[1] ?? 'list';

$statusFilter = null;
foreach (array_slice($argv, 2) as $arg) {
    if (str_starts_with($arg, '--status=')) {
        $statusFilter = TaskStatus::from(substr($arg, strlen('--status=')));
    }
}

switch ($command) {
    case 'list':
        $tasks = $statusFilter !== null ? $repository->byStatus($statusFilter) : $repository->all();
        if ($tasks === []) {
            echo "Немає задач за заданими фільтрами.\n";
            break;
        }
        foreach ($tasks as $task) {
            printTask($task);
        }
        break;

    case 'overdue':
        $now = new \DateTimeImmutable();
        $found = false;
        foreach ($repository->overdue($now) as $task) {
            printTask($task);
            $found = true;
        }
        if (!$found) {
            echo "Прострочених задач немає.\n";
        }
        break;

    default:
        fwrite(STDERR, "Невідома команда: {$command}. Доступні: list, overdue\n");
        exit(1);
}

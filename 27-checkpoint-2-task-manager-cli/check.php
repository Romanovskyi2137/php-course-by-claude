<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '/src/classes.php';

section('Вимога 1: TaskStatus');
test('backed enum з трьома case і правильними значеннями', function (): void {
    assertSame('todo', TaskStatus::Todo->value);
    assertSame('in_progress', TaskStatus::InProgress->value);
    assertSame('done', TaskStatus::Done->value);
});

section('Вимога 2: Task');
test('властивості доступні через promotion', function (): void {
    $due = new \DateTimeImmutable('2026-08-01');
    $task = new Task(1, 'Написати тести', TaskStatus::Todo, $due, 2);
    assertSame(1, $task->id);
    assertSame('Написати тести', $task->title);
    assertSame(TaskStatus::Todo, $task->status);
    assertSame($due, $task->dueDate);
    assertSame(2, $task->priority);
});
test('priority типово 3', function (): void {
    $task = new Task(1, 'X', TaskStatus::Todo, new \DateTimeImmutable('2026-08-01'));
    assertSame(3, $task->priority);
});
test('isOverdue() true для протермінованої незавершеної задачі', function (): void {
    $task = new Task(1, 'X', TaskStatus::Todo, new \DateTimeImmutable('2026-01-01'));
    assertTrue($task->isOverdue(new \DateTimeImmutable('2026-08-20')));
});
test('isOverdue() false для завершеної задачі, навіть протермінованої', function (): void {
    $task = new Task(1, 'X', TaskStatus::Done, new \DateTimeImmutable('2026-01-01'));
    assertSame(false, $task->isOverdue(new \DateTimeImmutable('2026-08-20')));
});
test('isOverdue() false для задачі з майбутнім дедлайном', function (): void {
    $task = new Task(1, 'X', TaskStatus::Todo, new \DateTimeImmutable('2099-01-01'));
    assertSame(false, $task->isOverdue(new \DateTimeImmutable('2026-08-20')));
});
test('withStatus() повертає новий обʼєкт, не змінюючи оригінал', function (): void {
    $due = new \DateTimeImmutable('2026-08-01');
    $original = new Task(1, 'X', TaskStatus::Todo, $due, 2);
    $updated = $original->withStatus(TaskStatus::Done);
    assertSame(TaskStatus::Todo, $original->status);
    assertSame(TaskStatus::Done, $updated->status);
    assertSame($original->id, $updated->id);
    assertSame($original->title, $updated->title);
    assertSame($original->priority, $updated->priority);
});
test('toArray() повертає скалярні значення, придатні для JSON', function (): void {
    $task = new Task(5, 'Ревʼю', TaskStatus::InProgress, new \DateTimeImmutable('2026-08-15'), 1);
    assertSame([
        'id' => 5,
        'title' => 'Ревʼю',
        'status' => 'in_progress',
        'dueDate' => '2026-08-15',
        'priority' => 1,
    ], $task->toArray());
});

section('Вимога 3: TaskRepository — базові операції');
function makeTask(int $id, TaskStatus $status = TaskStatus::Todo, string $due = '2026-08-01'): Task
{
    return new Task($id, "Task {$id}", $status, new \DateTimeImmutable($due));
}

test('add()/all()/count()', function (): void {
    $repo = new TaskRepository();
    $repo->add(makeTask(1));
    $repo->add(makeTask(2));
    assertSame(2, $repo->count());
    assertSame([1, 2], array_map(fn (Task $t) => $t->id, $repo->all()));
});
test('конструктор приймає початковий список задач', function (): void {
    $repo = new TaskRepository([makeTask(1), makeTask(2)]);
    assertSame(2, $repo->count());
});
test('findById() знаходить задачу або повертає null', function (): void {
    $repo = new TaskRepository([makeTask(1), makeTask(2)]);
    assertSame(2, $repo->findById(2)?->id);
    assertNull($repo->findById(999));
});
test('byStatus() фільтрує за статусом', function (): void {
    $repo = new TaskRepository([
        makeTask(1, TaskStatus::Todo),
        makeTask(2, TaskStatus::Done),
        makeTask(3, TaskStatus::Todo),
    ]);
    assertSame([1, 3], array_map(fn (Task $t) => $t->id, $repo->byStatus(TaskStatus::Todo)));
});

section('Вимога 4: TaskRepository::overdue()');
test('повертає саме Generator', function (): void {
    $repo = new TaskRepository();
    assertTrue($repo->overdue(new \DateTimeImmutable()) instanceof \Generator);
});
test('видає лише прострочені незавершені задачі, у порядку додавання', function (): void {
    $now = new \DateTimeImmutable('2026-08-20');
    $repo = new TaskRepository([
        makeTask(1, TaskStatus::Todo, '2026-01-01'),
        makeTask(2, TaskStatus::Done, '2026-01-01'),
        makeTask(3, TaskStatus::Todo, '2099-01-01'),
        makeTask(4, TaskStatus::InProgress, '2026-08-01'),
    ]);
    $overdueIds = array_map(fn (Task $t) => $t->id, iterator_to_array($repo->overdue($now)));
    assertSame([1, 4], $overdueIds);
});

section('Вимога 5: fromJsonFile() / toJsonFile()');
test('toJsonFile() записує, fromJsonFile() читає той самий стан', function (): void {
    $repo = new TaskRepository([
        makeTask(1, TaskStatus::Todo, '2026-08-01'),
        makeTask(2, TaskStatus::Done, '2026-07-01'),
    ]);
    $path = tempnam(sys_get_temp_dir(), 'tasks') . '.json';
    try {
        $repo->toJsonFile($path);
        $loaded = TaskRepository::fromJsonFile($path);
        assertSame(2, $loaded->count());
        assertSame(TaskStatus::Todo, $loaded->findById(1)?->status);
        assertSame('2026-07-01', $loaded->findById(2)?->dueDate->format('Y-m-d'));
    } finally {
        @unlink($path);
    }
});
test('fromJsonFile() кидає виняток при відсутньому файлі', function (): void {
    try {
        TaskRepository::fromJsonFile('/nonexistent/path/tasks.json');
        \Course\Check\fail('Очікував виняток при відсутньому файлі');
    } catch (\Throwable) {
        // очікувано — будь-який Throwable (RuntimeException/ValueError/тощо)
    }
});

exit(summary());

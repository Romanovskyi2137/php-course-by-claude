# Checkpoint 2: вимоги

## Мета

Зібрати в одному невеликому проєкті: `readonly`-властивості й
constructor promotion (тема 13), backed enum (тема 18), винятки (тема 21),
генератори (тема 22), дати (тема 23), JSON-персистенцію (тема 25) і
роботу з файлами (тема 26).

## Що робимо

Реалізуємо enum `TaskStatus` і два класи (`Task`, `TaskRepository`) у
`starter/src/classes.php`. CLI-обгортка вже написана за тебе в
`starter/bin/manager.php` — подивись туди, щоб зрозуміти, як класи
використовуються разом.

## Вимоги

1. Backed enum `TaskStatus: string` із трьома case: `Todo = 'todo'`,
   `InProgress = 'in_progress'`, `Done = 'done'`.

2. `readonly`-клас `Task` через constructor promotion:
   - властивості: `int $id`, `string $title`, `TaskStatus $status`,
     `\DateTimeImmutable $dueDate`, `int $priority = 3`;
   - `isOverdue(\DateTimeImmutable $now): bool` — `true`, якщо статус
     **не** `Done` і `dueDate` строго раніше за `$now`;
   - `withStatus(TaskStatus $status): self` — повертає **новий** `Task`
     з іншим статусом, решта полів — ті самі (оригінал не змінюється —
     властивості `readonly`, тож "зміна на місці" неможлива в принципі);
   - `toArray(): array` — `['id' => ..., 'title' => ..., 'status' =>
     ...->value, 'dueDate' => ...->format('Y-m-d'), 'priority' => ...]`.

3. Клас `TaskRepository` — сховище задач у пам'яті:
   - `__construct(array $tasks = [])`;
   - `add(Task $task): void` — додає задачу;
   - `all(): array` — повертає всі задачі списком;
   - `findById(int $id): ?Task` — знаходить за `id`, або `null`;
   - `byStatus(TaskStatus $status): array` — список задач з цим статусом;
   - `count(): int` — кількість задач.

4. `TaskRepository::overdue(\DateTimeImmutable $now): \Generator` —
   **лениво** (через `yield`) видає задачі, для яких
   `$task->isOverdue($now) === true`, у порядку додавання.

5. Персистенція через JSON-файл:
   - `TaskRepository::fromJsonFile(string $path): self` (static) —
     читає JSON-файл (масив об'єктів з полями `id`/`title`/`status`/
     `dueDate`/`priority`) і повертає репозиторій, заповнений `Task`;
   - `toJsonFile(string $path): void` — записує поточний стан
     репозиторію у файл за `$path` у тому ж форматі (через `toArray()`
     кожної задачі), читабельно (`JSON_PRETTY_PRINT`).

## Контракт (сигнатури)

```php
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
    )

    public function isOverdue(\DateTimeImmutable $now): bool
    public function withStatus(TaskStatus $status): self
    public function toArray(): array
}

final class TaskRepository
{
    public function __construct(array $tasks = [])
    public function add(Task $task): void
    public function all(): array
    public function findById(int $id): ?Task
    public function byStatus(TaskStatus $status): array
    public function count(): int
    public function overdue(\DateTimeImmutable $now): \Generator
    public static function fromJsonFile(string $path): self
    public function toJsonFile(string $path): void
}
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Усі властивості `Task` — саме `readonly`; `withStatus()` не намагається
  "обійти" це присвоєнням, а створює новий об'єкт.
- `overdue()` — саме генератор (`yield`), не функція, що спершу будує
  весь масив і повертає його.
- `fromJsonFile()`/`toJsonFile()` не ковтають помилки мовчки: некоректний
  JSON чи відсутній файл мають дати винятку дійти до викликача
  (`\JsonException` / `\RuntimeException` — на твій вибір, головне не
  `catch` без дії).

## Як перевірити

```bash
php check.php
```

Після зеленого `check.php` — запусти сам CLI:

```bash
php starter/bin/manager.php list
php starter/bin/manager.php list --status=todo
php starter/bin/manager.php overdue
```

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] `php starter/bin/manager.php overdue` реально показує прострочені
      задачі з фікстури `data/tasks.json`
- [ ] можеш пояснити, чому `withStatus()` повертає новий об'єкт, а не
      змінює `$this->status`

## Підказки

<details>
<summary>Підказка 1 — Task</summary>

```php
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
```
</details>

<details>
<summary>Підказка 2 — overdue()</summary>

```php
public function overdue(\DateTimeImmutable $now): \Generator
{
    foreach ($this->tasks as $task) {
        if ($task->isOverdue($now)) {
            yield $task;
        }
    }
}
```
</details>

<details>
<summary>Підказка 3 — fromJsonFile()/toJsonFile()</summary>

```php
public static function fromJsonFile(string $path): self
{
    $raw = @file_get_contents($path); // @ — щоб не дублювати вбудований E_WARNING власним винятком нижче
    if ($raw === false) {
        throw new \RuntimeException("Не вдалось прочитати {$path}");
    }

    $rows = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);
    $tasks = array_map(
        static fn (array $row) => new Task(
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
    $rows = array_map(static fn (Task $task) => $task->toArray(), $this->tasks);
    file_put_contents(
        $path,
        json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
    );
}
```
</details>

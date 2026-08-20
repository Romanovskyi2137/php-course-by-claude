# Checkpoint 2: Task manager CLI

Другий checkpoint-проєкт курсу. Мета — зібрати докупи блок B (ООП: теми
12-21) і блок C (стандартна бібліотека: теми 22-26) в один невеликий CLI
для керування задачами: `readonly`-DTO, backed enum, репозиторій, генератор
для лінивого фільтрування, JSON-персистенція через файл.

## Що будуємо

CLI-утиліту, яка завантажує список задач із JSON-файлу, вміє показати всі
задачі (опційно відфільтровані за статусом) і показати список
прострочених задач — усе на основі невеликого, але реалістичного набору
класів: `Task` (незмінний DTO), `TaskStatus` (backed enum) і
`TaskRepository` (сховище в пам'яті з JSON-персистенцією).

## Структура

```
27-checkpoint-2-task-manager-cli/
  README.md          # цей файл
  task.md             # вимоги, контракт, як перевірити
  data/
    tasks.json          # фікстура: список задач у JSON
  starter/
    src/classes.php       # ТУТ ти пишеш код — enum + 2 класи з TODO
    bin/manager.php          # готовий скрипт-обгортка для CLI (не редагуй)
  solution/
    src/classes.php           # референсний розв'язок
    bin/manager.php              # той самий скрипт-обгортка
  check.php                       # автоматична перевірка класів із src/classes.php
```

## Як працювати

1. Прочитай `task.md`.
2. Редагуй `starter/src/classes.php`.
3. Перевіряй: `php check.php`.
4. Коли `check.php` зелений — запусти реальний CLI:
   ```bash
   php starter/bin/manager.php list
   php starter/bin/manager.php list --status=todo
   php starter/bin/manager.php overdue
   ```
5. Порівняй з `solution/`, заціни чекбокс у `CURRICULUM.md`, зроби коміт.

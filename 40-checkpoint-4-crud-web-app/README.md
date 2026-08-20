# Checkpoint 4: CRUD-каталог

Четвертий checkpoint-проєкт курсу. Мета — зібрати докупи блок E (дані та
інструменти: теми 35-39) у застосунок керування каталогом продуктів:
Composer + PSR-4, PDO-репозиторій із prepared statements, і власний
маленький механізм міграцій схеми.

## Що будуємо

CRUD-застосунок (Create/Read/Update/Delete) для каталогу продуктів:
список, форма додавання, редагування, видалення. Схема БД (`categories`,
`products`) створюється не вручну через `mysql`-CLI, а через файли
міграцій у `migrations/`, застосовані власним скриптом `bin/migrate.php`.

## Структура

```
40-checkpoint-4-crud-web-app/
  README.md          # цей файл
  task.md             # вимоги, контракт, як перевірити
  starter/
    composer.json        # готовий, з autoload.psr-4 "App\\": "src/"
    src/
      Product.php           # ТУТ ти пишеш код — readonly DTO з TODO
      ProductRepository.php   # ТУТ ти пишеш код — PDO-репозиторій з TODO
      Migrator.php              # ТУТ ти пишеш код — Migrator::pending() з TODO
    migrations/
      0001_create_categories_table.sql
      0002_create_products_table.sql
      0003_seed_categories.sql
    bin/
      migrate.php              # готовий код — застосовує міграції (не редагуй)
    public/
      index.php, create.php, store.php, edit.php, update.php, delete.php
                                # готовий код — CRUD-UI (не редагуй)
  solution/
    ...                         # той самий набір, повністю реалізований
  check.php                      # автоматична перевірка src/*.php
```

## Як працювати

1. Прочитай `task.md`.
2. `cd starter && composer dump-autoload && cd ..` (потрібно один раз,
   поки в `src/` ще TODO — Composer просто зареєструє мапінг).
3. Редагуй `starter/src/Product.php`, `ProductRepository.php`,
   `Migrator.php`.
4. Перевіряй: `php check.php`.
5. Коли `check.php` зелений:
   ```bash
   php starter/bin/migrate.php
   php -S localhost:8000 -t starter/public
   ```
6. Відкрий `http://localhost:8000/`, попрацюй із каталогом.
7. Порівняй з `solution/`, заціни чекбокс у `CURRICULUM.md`, зроби коміт.

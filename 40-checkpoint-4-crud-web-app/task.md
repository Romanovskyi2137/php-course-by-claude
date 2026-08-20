# Checkpoint 4: вимоги

## Підготовка

```bash
docker compose -f _env/docker-compose.yml up -d
cp _env/config.example.php _env/config.local.php   # якщо ще не зробив
cd 40-checkpoint-4-crud-web-app/starter && composer dump-autoload && cd ..
```

## Мета

Зібрати в одному проєкті: PSR-4-автозавантаження (тема 38), PDO CRUD
через prepared statements (теми 36-37), `readonly`-DTO (тема 13) і
міграції схеми — окремий SQL-файл на зміну, застосовується один раз і
відстежується.

## Що робимо

Реалізуємо три класи в `starter/src/`: `Product`, `ProductRepository`,
`Migrator`. `starter/bin/migrate.php` і `starter/public/*.php` вже
написані за тебе — подивись туди, щоб зрозуміти, як усе працює разом.

## Вимоги

1. `readonly`-клас `Product` через constructor promotion: `int $id`,
   `string $name`, `int $categoryId`, `float $price`, `bool $inStock`.

2. Клас `ProductRepository`:
   - `__construct(\PDO $pdo)`;
   - `findAll(): array` — усі продукти як `Product[]`, відсортовані за `id`;
   - `findById(int $id): ?Product`;
   - `create(string $name, int $categoryId, float $price, bool
     $inStock): int` — вставляє рядок, повертає новий `id`;
   - `update(int $id, string $name, int $categoryId, float $price, bool
     $inStock): bool` — `true`, якщо рядок знайдено й змінено;
   - `delete(int $id): bool` — `true`, якщо рядок знайдено й видалено.

3. `Migrator::pending(array $allMigrationFiles, array
   $appliedMigrationNames): array` (статичний метод) — які файли з
   `$allMigrationFiles` **відсутні** в `$appliedMigrationNames`,
   відсортовані за іменем (це і є порядок застосування — файли
   міграцій названі з числовим префіксом: `0001_...`, `0002_...`).

## Контракт (сигнатури)

```php
namespace App;

final class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $categoryId,
        public readonly float $price,
        public readonly bool $inStock,
    )
}

final class ProductRepository
{
    public function __construct(\PDO $pdo)
    public function findAll(): array
    public function findById(int $id): ?Product
    public function create(string $name, int $categoryId, float $price, bool $inStock): int
    public function update(int $id, string $name, int $categoryId, float $price, bool $inStock): bool
    public function delete(int $id): bool
}

final class Migrator
{
    public static function pending(array $allMigrationFiles, array $appliedMigrationNames): array
}
```

## Обмеження

- Кожен файл починається з `declare(strict_types=1);`, одразу після —
  `namespace App;`.
- `ProductRepository` використовує **лише** `prepare()`/`execute()` з
  плейсхолдерами — жодної конкатенації значень прямо в SQL-рядок.
- `price` з БД (`DECIMAL`) приходить рядком — `ProductRepository`
  явно приводить його до `float` при побудові `Product` (і `in_stock`
  — до `bool`), інакше конструктор `Product` кине `TypeError`
  (`declare(strict_types=1)` не приводить типи автоматично).

## Як перевірити

```bash
php check.php
```

Після зеленого `check.php` — застосуй міграції й запусти застосунок:

```bash
php starter/bin/migrate.php
php -S localhost:8000 -t starter/public
```

Відкрий `http://localhost:8000/` — список продуктів (спершу порожній),
додай кілька, відредагуй, видали.

## Definition of Done

- [ ] усі перевірки check.php зелені (або чесний `[skip]`, якщо MySQL
      не піднятий)
- [ ] `php starter/bin/migrate.php` реально створює таблиці й повторний
      запуск нічого не застосовує вдруге (виводить "нема нових міграцій")
- [ ] CRUD в браузері працює: додати, відредагувати, видалити продукт
- [ ] можеш пояснити, чому `ProductRepository::create()` приймає
      скалярні параметри (`string`, `int`, `float`, `bool`), а не
      готовий об'єкт `Product` (в якого вже мав би бути `id`, якого
      ще не існує до вставки)

## Підказки

<details>
<summary>Підказка 1 — ProductRepository::findAll()/hydrate</summary>

```php
public function findAll(): array
{
    $stmt = $this->pdo->query('SELECT * FROM products ORDER BY id');

    return array_map($this->hydrate(...), $stmt->fetchAll());
}

private function hydrate(array $row): Product
{
    return new Product(
        id: (int) $row['id'],
        name: $row['name'],
        categoryId: (int) $row['category_id'],
        price: (float) $row['price'],
        inStock: (bool) $row['in_stock'],
    );
}
```
</details>

<details>
<summary>Підказка 2 — ProductRepository::create()/update()/delete()</summary>

```php
public function create(string $name, int $categoryId, float $price, bool $inStock): int
{
    $stmt = $this->pdo->prepare(
        'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $categoryId, $price, $inStock ? 1 : 0]);

    return (int) $this->pdo->lastInsertId();
}

public function delete(int $id): bool
{
    $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);

    return $stmt->rowCount() > 0;
}
```
</details>

<details>
<summary>Підказка 3 — Migrator::pending()</summary>

```php
public static function pending(array $allMigrationFiles, array $appliedMigrationNames): array
{
    $pending = array_values(array_diff($allMigrationFiles, $appliedMigrationNames));
    sort($pending);

    return $pending;
}
```
</details>

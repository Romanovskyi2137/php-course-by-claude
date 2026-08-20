# Завдання 37: prepared statements, CRUD і транзакції

## Підготовка

```bash
docker compose -f _env/docker-compose.yml up -d
cp _env/config.example.php _env/config.local.php   # якщо ще не зробив
```

## Мета

1. Вставити кілька рядків атомарно — все або нічого.
2. Оновити рядок через prepared statement, дізнатись, чи щось змінилось.
3. Отримати сторінку результатів через `LIMIT`/`OFFSET`.
4. Уникнути N+1: один `JOIN`-запит замість запиту в циклі.

## Вимоги

1. `insert_products_batch(\PDO $pdo, array $products)` — `$products` —
   список `['name' => string, 'price' => float]`. Вставляє всі рядки
   **в одній транзакції** (`category_id` — постав `1`); повертає список
   `id` нових рядків у тому самому порядку. Якщо хоч один `INSERT`
   провалюється — відкочує **всі** вставки цього виклику (жодна не
   лишається в БД) і прокидає виняток далі.
2. `update_price_by_id(\PDO $pdo, int $id, float $price)` — оновлює
   `price` продукту з вказаним `id` через prepared statement; повертає
   `true`, якщо рядок було знайдено й змінено, `false` — якщо рядка з
   таким `id` немає.
3. `fetch_products_page(\PDO $pdo, int $page, int $perPage)` — сторінка
   продуктів, відсортованих за `id` за зростанням (`page` рахується з 1).
4. `fetch_products_with_categories(\PDO $pdo)` — усі продукти з назвою
   категорії, **одним запитом** (не в циклі!) — `['id', 'name',
   'category_name']` для кожного, відсортовані за `id` продукту.

## Контракт (сигнатури)

```php
function insert_products_batch(\PDO $pdo, array $products): array
function update_price_by_id(\PDO $pdo, int $id, float $price): bool
function fetch_products_page(\PDO $pdo, int $page, int $perPage): array
function fetch_products_with_categories(\PDO $pdo): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `insert_products_batch()` використовує `beginTransaction()`/`commit()`/
  `rollBack()` — не просто виконує вставки одна за одною без транзакції.
- `fetch_products_page()` передає `LIMIT`/`OFFSET` через `bindValue()` з
  `PDO::PARAM_INT`.
- `fetch_products_with_categories()` робить рівно **один** запит до
  БД — без циклу з запитом усередині.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені (або чесний `[skip]`, якщо MySQL не піднятий)
- [ ] можеш пояснити, чому `id`, згенеровані невдалою (відкоченою)
      спробою `insert_products_batch()`, можуть "пропасти" з послідовності
      назавжди (lecture.md, розділ 4)

## Бонус (не перевіряється автоматично)

Додай `count_products(\PDO $pdo): int` і використай його разом з
`fetch_products_page()`, щоб порахувати загальну кількість сторінок —
типова пара для будь-якого UI з пагінацією.

<details>
<summary>Підказка 1</summary>

```php
function insert_products_batch(\PDO $pdo, array $products): array
{
    $pdo->beginTransaction();
    try {
        $ids = [];
        $stmt = $pdo->prepare('INSERT INTO products (name, category_id, price) VALUES (?, 1, ?)');
        foreach ($products as $product) {
            $stmt->execute([$product['name'], $product['price']]);
            $ids[] = (int) $pdo->lastInsertId();
        }
        $pdo->commit();

        return $ids;
    } catch (\PDOException $e) {
        $pdo->rollBack();

        throw $e;
    }
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function update_price_by_id(\PDO $pdo, int $id, float $price): bool
{
    $stmt = $pdo->prepare('UPDATE products SET price = ? WHERE id = ?');
    $stmt->execute([$price, $id]);

    return $stmt->rowCount() > 0;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function fetch_products_page(\PDO $pdo, int $page, int $perPage): array
{
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare('SELECT * FROM products ORDER BY id LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $perPage, \PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function fetch_products_with_categories(\PDO $pdo): array
{
    return $pdo->query(
        'SELECT p.id, p.name, c.name AS category_name
         FROM products p
         JOIN categories c ON p.category_id = c.id
         ORDER BY p.id'
    )->fetchAll();
}
```
</details>

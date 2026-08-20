# Завдання 36: PDO — з'єднання та запити

## Підготовка

```bash
docker compose -f _env/docker-compose.yml up -d
cp _env/config.example.php _env/config.local.php   # якщо ще не зробив
```

## Мета

1. Створити PDO-з'єднання з масиву конфігурації.
2. Вибрати всі рядки таблиці.
3. Вибрати один рядок як типізований об'єкт (`FETCH_CLASS`).
4. Вставити рядок і дістати його `AUTO_INCREMENT`-id.

## Вимоги

1. `create_connection(array $config)` — `$config` має ключі `host`,
   `port`, `database`, `charset`, `username`, `password` (як у
   `_env/config.example.php['db']`). Будує DSN і повертає `\PDO` з
   `ATTR_ERRMODE => ERRMODE_EXCEPTION` і
   `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`.
2. `fetch_all_products(\PDO $pdo)` — усі рядки таблиці `products`,
   відсортовані за `id`, як асоціативні масиви.
3. `class ProductRow` з публічними властивостями `int $id`,
   `string $name`, `string $price`; `fetch_product_as_object(\PDO $pdo,
   int $id)` — один продукт як `ProductRow` через `PDO::FETCH_CLASS`,
   або `null`, якщо не знайдено.
4. `insert_product(\PDO $pdo, string $name, float $price)` — вставляє
   новий продукт (`category_id` і `in_stock` — постав `1` за
   замовчуванням) і повертає його `id` через `lastInsertId()`.

## Контракт (сигнатури)

```php
function create_connection(array $config): \PDO
function fetch_all_products(\PDO $pdo): array

class ProductRow
{
    public int $id;
    public string $name;
    public string $price;
}

function fetch_product_as_object(\PDO $pdo, int $id): ?ProductRow
function insert_product(\PDO $pdo, string $name, float $price): int
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `create_connection()` явно виставляє `ATTR_ERRMODE` і
  `ATTR_DEFAULT_FETCH_MODE` — не покладається на типові значення PDO.
- `fetch_product_as_object()` використовує `prepare()`/`execute()` з
  плейсхолдером для `$id`, не підставляє його прямо в рядок SQL.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені (або чесний `[skip]`, якщо MySQL не піднятий)
- [ ] можеш пояснити, чому `price` у `ProductRow` — `string`, а не `float`

## Бонус (не перевіряється автоматично)

Додай `fetch_products_as_objects(\PDO $pdo): array` — усі продукти як
масив `ProductRow`, тим самим `FETCH_CLASS`-механізмом, але для
множинної вибірки (`fetchAll(PDO::FETCH_CLASS, ProductRow::class)`).

<details>
<summary>Підказка 1</summary>

```php
function create_connection(array $config): \PDO
{
    $dsn = "mysql:host={$config['host']};port={$config['port']};"
        . "dbname={$config['database']};charset={$config['charset']}";

    return new \PDO($dsn, $config['username'], $config['password'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function fetch_product_as_object(\PDO $pdo, int $id): ?ProductRow
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, ProductRow::class);

    $product = $stmt->fetch();

    return $product === false ? null : $product;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function insert_product(\PDO $pdo, string $name, float $price): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, 1, ?, 1)'
    );
    $stmt->execute([$name, $price]);

    return (int) $pdo->lastInsertId();
}
```
</details>

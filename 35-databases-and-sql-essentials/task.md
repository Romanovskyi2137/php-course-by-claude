# Завдання 35: бази даних та основи SQL

## Підготовка

```bash
docker compose -f _env/docker-compose.yml up -d
cp _env/config.example.php _env/config.local.php   # якщо ще не зробив
```

Якщо MySQL не піднятий, `check.php` виведе `[skip]` з підказкою — не
провалиться незрозумілою помилкою з'єднання.

## Мета

Написати чотири SQL-запити як рядки — `check.php` виконає їх напряму
через PDO проти реальної, щойно створеної схеми (`categories`,
`products`), і звірить результат. Це не про PHP — про сам SQL.

## Схема (створюється автоматично в check.php, нічого створювати вручну)

```sql
CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL);
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

Фікстура: категорії `(1, 'Інструменти')`, `(2, 'Електроніка')`; продукти
`(1, 'Дриль', 1, 45.00, 1)`, `(2, 'Навушники', 2, 35.50, 1)`,
`(3, 'Клавіатура', 2, 25.00, 0)`.

## Вимоги

1. `select_in_stock_products_sql()` — SQL, що вибирає всі колонки
   продуктів з `in_stock = 1`, відсортованих за `id` за зростанням.
2. `select_products_with_category_sql()` — SQL, що через `JOIN`
   повертає для кожного продукту (відсортовано за `id` продукту)
   колонки `id`, `product_name` (назва продукту), `category_name`
   (назва категорії).
3. `insert_product_sql()` — SQL з чотирма `?`-плейсхолдерами для
   `INSERT` нового продукту: `name`, `category_id`, `price`, `in_stock`
   (у цьому порядку).
4. `update_price_sql()` — SQL з двома `?`-плейсхолдерами: оновлює
   `price` продукту за `id` (у цьому порядку: нова ціна, потім id).

## Контракт (сигнатури)

```php
function select_in_stock_products_sql(): string
function select_products_with_category_sql(): string
function insert_product_sql(): string
function update_price_sql(): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Функції повертають лише **рядок SQL** — не виконують запит самі
  (виконання — робота `check.php`, це навмисно, щоб ти писав SQL, а не
  PHP-код навколо нього).
- `insert_product_sql()`/`update_price_sql()` використовують
  `?`-плейсхолдери, не підставляють значення прямо в рядок.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені (або чесний `[skip]`, якщо MySQL не піднятий)
- [ ] можеш пояснити, чому `JOIN` (вимога 2) поверне лише продукти, для
      яких існує категорія — а не всі продукти взагалі

## Бонус (не перевіряється автоматично)

Відкрий `http://localhost:8080` (Adminer), увійди (сервер `mysql`, юзер/
пароль `phpcourse`/`phpcourse`, БД `phpcourse`) і подивись на дані
візуально після того, як `check.php` створить і заповнить таблиці.

<details>
<summary>Підказка 1</summary>

```php
function select_in_stock_products_sql(): string
{
    return 'SELECT * FROM products WHERE in_stock = 1 ORDER BY id';
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function select_products_with_category_sql(): string
{
    return 'SELECT p.id, p.name AS product_name, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.id';
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function insert_product_sql(): string
{
    return 'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, ?, ?, ?)';
}

function update_price_sql(): string
{
    return 'UPDATE products SET price = ? WHERE id = ?';
}
```
</details>

# 35. Бази даних та основи SQL

> Що ти вже вмієш: SQL з іншої мови/курсу — сам SQL майже однаковий
> скрізь · Що додає ця тема: підняти реальний MySQL локально й попрацювати
> з ним напряму, до того, як за це візьметься PHP (тема 36) · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- SQL — не PHP-специфіка: `SELECT`/`JOIN`/`INSERT`/`UPDATE` виглядають
  практично однаково незалежно від мови застосунку — ця тема радше
  освіжає SQL і показує MySQL-специфічні деталі, ніж вчить SQL з нуля.
- Ключова відмінність цієї теми — **інструмент**: MySQL тут піднятий
  через Docker (`_env/docker-compose.yml`), а не хмарний сервіс чи
  вбудована SQLite. Це найближче до того, як реально виглядає розробка
  в команді: локальна копія тієї самої СУБД, що й у продакшені.
- `mysql`-CLI (клієнт командного рядка) — аналог `psql` (PostgreSQL) чи
  `mongosh` (MongoDB): підключаєшся, пишеш SQL напряму, бачиш результат
  одразу, без застосунку-посередника.

## 2. Ключові терміни

- **Schema (схема)** — структура БД: таблиці, колонки, типи, зв'язки
  (не плутати зі "схемою валідації" з інших контекстів).
- **Primary key (первинний ключ)** — колонка (чи набір колонок), що
  унікально ідентифікує рядок; у MySQL типово `AUTO_INCREMENT INT`.
- **Foreign key (зовнішній ключ)** — колонка, що посилається на первинний
  ключ іншої таблиці, забезпечуючи цілісність зв'язку (не можна вставити
  `category_id`, якого немає в `categories`).
- **`JOIN`** — об'єднання рядків з кількох таблиць за умовою зв'язку
  (типово — рівність зовнішнього й первинного ключа).

## 3. Теорія

### 3.1 Піднімаємо MySQL локально

```bash
docker compose -f _env/docker-compose.yml up -d
```

Піднімає MySQL 8.4 на `localhost:3306` (БД `phpcourse`, юзер/пароль
`phpcourse`/`phpcourse`) і Adminer (веб-інтерфейс перегляду БД) на
`localhost:8080`. Деталі — `_env/README.md`.

### 3.2 `CREATE TABLE`, `PRIMARY KEY`, `FOREIGN KEY`

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

`DECIMAL(10,2)`, не `FLOAT`/`DOUBLE`, для грошей — точне десяткове
представлення без похибок округлення, властивих числам з рухомою комою.

### 3.3 `SELECT`, `WHERE`, `ORDER BY`

```sql
SELECT * FROM products WHERE in_stock = 1 ORDER BY price DESC;

SELECT name, price FROM products WHERE price > 30.00;
```

### 3.4 `JOIN`: дані з кількох таблиць за раз

```sql
SELECT p.id, p.name AS product_name, c.name AS category_name
FROM products p
JOIN categories c ON p.category_id = c.id
ORDER BY p.id;
```

`JOIN` (він же `INNER JOIN`) повертає лише рядки, для яких знайшлося
співпадіння в обох таблицях. Псевдоніми (`p`, `c`) роблять запит
коротшим і читабельнішим, коли колонки з однаковими іменами (`id`,
`name`) є в обох таблицях.

### 3.5 `INSERT`, `UPDATE`

```sql
INSERT INTO products (name, category_id, price, in_stock)
VALUES ('Монітор', 2, 150.00, 1);

UPDATE products SET price = 40.00, in_stock = 0 WHERE id = 1;
```

`UPDATE` **без** `WHERE` оновлює **всі** рядки таблиці — одна з
найнебезпечніших одрядкових помилок у SQL; завжди перевіряй `WHERE`
перед виконанням `UPDATE`/`DELETE` на реальних даних.

## 4. Підводні камені

- `FLOAT`/`DOUBLE` для грошових сум — джерело неточностей округлення
  (`0.1 + 0.2 !== 0.3`, тема 03, стосується будь-якої мови з IEEE 754) —
  для грошей завжди `DECIMAL(10,2)`.
- Забутий `WHERE` в `UPDATE`/`DELETE` оновлює/видаляє **весь** вміст
  таблиці — перед виконанням на реальних даних корисно спершу
  прогнати той самий `WHERE` як `SELECT`, побачити, які саме рядки
  зачепить зміна.
- `JOIN` без `ON`-умови (чи з неправильною умовою) дає **декартів
  добуток** — кожен рядок однієї таблиці з кожним рядком іншої;
  для 100 продуктів і 10 категорій це 1000 рядків замість очікуваних 100.

## 5. Сучасний PHP 8.3+

Не стосується PHP напряму — MySQL 8.4 (LTS-версія, використана в
`docker-compose.yml`) підтримує сучасний SQL: віконні функції, CTE
(`WITH ...`), JSON-колонки нативно. Курс торкнеться лише базового SQL —
цього достатньо для 95% прикладного веб-коду.

## 6. Контекст екосистеми

Adminer (піднятий поряд з MySQL, `localhost:8080`) — легкий
веб-інтерфейс для перегляду БД без встановлення окремого GUI-клієнта
(на кшталт TablePlus/DBeaver) — зручно перевірити результат `INSERT`/
`UPDATE` візуально, поки звикаєш до SQL.

## 7. Документація

- https://dev.mysql.com/doc/refman/8.4/en/select.html
- https://dev.mysql.com/doc/refman/8.4/en/join.html
- https://dev.mysql.com/doc/refman/8.4/en/data-types.html

## 8. Питання для самоперевірки

1. Чому для грошових сум використовують `DECIMAL`, а не `FLOAT`?
2. Що поверне `UPDATE products SET price = 0;` без `WHERE`?
3. Що таке декартів добуток і коли `JOIN` його дає?
4. Навіщо потрібен `FOREIGN KEY`, якщо можна просто зберігати
   `category_id` як звичайний `INT`?

# 36. PDO: з'єднання та запити

> Що ти вже вмієш: JDBC/psycopg2/`mysql2`-подібний клієнт БД з іншої
> мови · Що додає ця тема: PDO — єдиний PHP-інтерфейс до **будь-якої**
> реляційної БД (MySQL, PostgreSQL, SQLite — той самий API) · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- PDO (PHP Data Objects) — приблизно те, що JDBC для Java: єдиний
  абстрактний інтерфейс поверх різних СУБД. На відміну від Node, де
  типово окремий пакет під кожну БД (`mysql2`, `pg`) з несумісним API,
  PDO дає **той самий** `PDO`/`PDOStatement`-клас незалежно від того,
  MySQL це, PostgreSQL чи SQLite — міняється лише DSN (рядок з'єднання).
- На відміну від голого `mysqli` (старіше, MySQL-специфічне
  розширення PHP), PDO — рекомендований сучасний вибір: підтримує
  prepared statements (тема 37) для всіх драйверів однаково й не
  прив'язує код до конкретної СУБД.
- Обробка помилок через винятки (`PDOException`, тема 21), а не через
  перевірку кожного повернутого значення на `false` — ближче до
  типового підходу винятків у JS/Python/Java, ніж до класичного
  C-style error-checking в старому PHP-коді.

## 2. Ключові терміни

- **DSN (Data Source Name)** — рядок, що описує, як підключитись до БД:
  драйвер, хост, порт, назва БД, кодування (`mysql:host=...;dbname=...`).
- **`PDO::ATTR_ERRMODE`** — режим обробки помилок; `ERRMODE_EXCEPTION`
  (рекомендовано) кидає `PDOException` замість мовчазного `false`.
- **Fetch mode (режим вибірки)** — як саме PDO перетворює рядок
  результату на PHP-значення: асоціативний масив (`FETCH_ASSOC`),
  об'єкт конкретного класу (`FETCH_CLASS`), `stdClass` (`FETCH_OBJ`).
- **`lastInsertId()`** — метод, що повертає `AUTO_INCREMENT`-значення,
  згенероване останнім `INSERT`.

## 3. Теорія

### 3.1 Підключення: DSN + атрибути

```php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=phpcourse;charset=utf8mb4';

$pdo = new \PDO($dsn, 'phpcourse', 'phpcourse', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
]);
```

`ATTR_ERRMODE => ERRMODE_EXCEPTION` — перше, що варто виставити:
типовий режим PDO (`ERRMODE_SILENT`) залишає помилки непоміченими,
доки не звернешся до `errorInfo()` вручну щоразу.

### 3.2 Прості запити: `query()` vs `exec()`

```php
$stmt = $pdo->query('SELECT * FROM products'); // для SELECT — повертає PDOStatement
$rows = $stmt->fetchAll(); // FETCH_ASSOC, бо виставлено як типовий вище

$affected = $pdo->exec('UPDATE products SET in_stock = 0'); // для UPDATE/DELETE/DDL — повертає int (кількість рядків)
```

`query()`/`exec()` без плейсхолдерів годяться лише для **довірених**,
без користувацького вводу запитів — щойно з'являється зовнішнє
значення, потрібен `prepare()`/`execute()` (тема 37) проти SQL-ін'єкцій.

### 3.3 Fetch modes: як саме отримати рядок

```php
$stmt = $pdo->query('SELECT * FROM products WHERE id = 1');

$stmt->fetch(\PDO::FETCH_ASSOC); // ['id' => 1, 'name' => 'Дриль', ...]
$stmt->fetch(\PDO::FETCH_OBJ);   // stdClass {id: 1, name: 'Дриль', ...}

class ProductRow
{
    public int $id;
    public string $name;
    public string $price; // DECIMAL з MySQL повертається як string, не float!
}

$stmt->setFetchMode(\PDO::FETCH_CLASS, ProductRow::class);
$product = $stmt->fetch(); // об'єкт ProductRow, властивості заповнені напряму
```

`FETCH_CLASS` заповнює публічні властивості класу **напряму**, минаючи
конструктор (властивості виставляються до виклику `__construct()`, якщо
він є) — зручно для простих DTO-подібних класів "рядок таблиці = об'єкт".

### 3.4 `lastInsertId()`

```php
$pdo->exec("INSERT INTO products (name, price) VALUES ('Монітор', 150.00)");
$newId = (int) $pdo->lastInsertId(); // AUTO_INCREMENT-значення нового рядка
```

## 4. Підводні камені

- `DECIMAL`-колонки MySQL повертаються PDO як **рядки** (`"45.00"`), не
  `float` — щоб не втратити точність при конвертації (тема 03, IEEE
  754). Порівнюй їх через `(float)`-приведення чи `bccomp()`, не `===`
  з голим числом.
- Забутий `ATTR_ERRMODE => ERRMODE_EXCEPTION` — помилки запиту (типова
  друкарська помилка в назві колонки) провалюються мовчки, `fetch()`
  просто поверне `false`, і баг проявиться значно пізніше й незрозуміліше.
- `lastInsertId()` повертає ID **останнього** `INSERT` у поточному
  з'єднанні — виклик після кількох вставок підряд без проміжного
  читання дає ID останньої з них, не першої.

## 5. Сучасний PHP 8.3+

PDO — стабільний, давно усталений API. Значна зміна сталась у PHP 8.1:
`PDO::ERRMODE_EXCEPTION` став **типовим** режимом за замовчуванням для
нових з'єднань (раніше типовим був мовчазний `ERRMODE_SILENT`) — але
явно виставляти його в конструкторі й далі гарна практика: код лишається
однозначно читабельним незалежно від версії PHP чи конфігурації.

## 6. Контекст екосистеми

ORM-и (Doctrine, Eloquent у Laravel) використовують PDO **під капотом**
— вони не замінюють його, а додають шар абстракції поверх (об'єкти
замість "сирих" рядків, автоматичні `JOIN`-и через звʼязки). Розуміння
PDO напряму — передумова розуміти, що саме ORM робить за тебе й де
шукати проблему, коли щось працює не так, як очікувалось.

## 7. Документація

- https://www.php.net/manual/en/book.pdo.php
- https://www.php.net/manual/en/pdo.constants.php
- https://www.php.net/manual/en/pdostatement.fetch.php

## 8. Питання для самоперевірки

1. Чим PDO відрізняється від `mysqli` з точки зору переносимості між СУБД?
2. Що саме змінює `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`?
3. Чому `DECIMAL`-значення з MySQL приходять у PHP рядком, а не `float`?
4. Коли варто брати `query()`, а коли обов'язково `prepare()`/`execute()`?

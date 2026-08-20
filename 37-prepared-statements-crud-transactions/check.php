<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertSame, assertThrows, assertTrue, connectTestDatabase, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

class QueryCountingPdo extends \PDO
{
    public int $queryCount = 0;

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs)
    {
        $this->queryCount++;

        return parent::query($query, $fetchMode, ...$fetchModeArgs);
    }

    public function prepare(string $query, array $options = [])
    {
        $this->queryCount++;

        return parent::prepare($query, $options);
    }
}

$pdo = connectTestDatabase();
if ($pdo === null) {
    exit(summary());
}

function resetSchema(\PDO $pdo): void
{
    $pdo->exec('DROP TABLE IF EXISTS products');
    $pdo->exec('DROP TABLE IF EXISTS categories');
    $pdo->exec('CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL)');
    $pdo->exec('CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        category_id INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        in_stock TINYINT(1) NOT NULL DEFAULT 1,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )');
    $pdo->exec("INSERT INTO categories (id, name) VALUES (1, 'Інструменти'), (2, 'Електроніка')");
}

section('Вимога 1: insert_products_batch()');
test('вставляє всі рядки в одній транзакції, повертає їх id', function () use ($pdo): void {
    resetSchema($pdo);
    $ids = insert_products_batch($pdo, [
        ['name' => 'A', 'price' => 10.0],
        ['name' => 'B', 'price' => 20.0],
    ]);
    assertSame(2, count($ids));
    assertSame(2, (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn());
});
test('провал одного INSERT відкочує всю партію (нічого не лишається)', function () use ($pdo): void {
    resetSchema($pdo);
    assertThrows(\PDOException::class, function () use ($pdo): void {
        insert_products_batch($pdo, [
            ['name' => 'OK', 'price' => 10.0],
            ['name' => str_repeat('x', 200), 'price' => 20.0], // задовге для VARCHAR(50)
        ]);
    });
    assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn());
});

section('Вимога 2: update_price_by_id()');
test('true і реально змінює ціну для наявного id', function () use ($pdo): void {
    resetSchema($pdo);
    [$id] = insert_products_batch($pdo, [['name' => 'A', 'price' => 10.0]]);
    assertSame(true, update_price_by_id($pdo, $id, 99.0));
    $stmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
    $stmt->execute([$id]);
    assertEquals(99.0, $stmt->fetchColumn());
});
test('false для неіснуючого id, нічого не ламає', function () use ($pdo): void {
    assertSame(false, update_price_by_id($pdo, 999999, 1.0));
});

section('Вимога 3: fetch_products_page()');
test('повертає правильну сторінку, відсортовану за id', function () use ($pdo): void {
    resetSchema($pdo);
    insert_products_batch($pdo, array_map(
        static fn (int $i) => ['name' => "P{$i}", 'price' => (float) $i],
        range(1, 10)
    ));
    $page2 = fetch_products_page($pdo, 2, 3);
    assertSame(3, count($page2));
    assertSame(['P4', 'P5', 'P6'], array_column($page2, 'name'));
});

section('Вимога 4: fetch_products_with_categories()');
test('повертає id/name/category_name, робить рівно один запит до БД', function () use ($pdo): void {
    resetSchema($pdo);
    $stmt = $pdo->prepare('INSERT INTO products (name, category_id, price) VALUES (?, ?, ?)');
    $stmt->execute(['Дриль', 1, 45.0]);
    $stmt->execute(['Навушники', 2, 35.5]);

    $countingConfig = (require __DIR__ . '/../_env/config.local.php')['db'];
    $dsn = "mysql:host={$countingConfig['host']};port={$countingConfig['port']};"
        . "dbname={$countingConfig['database']};charset={$countingConfig['charset']}";
    $countingPdo = new QueryCountingPdo($dsn, $countingConfig['username'], $countingConfig['password'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);

    $rows = fetch_products_with_categories($countingPdo);

    assertSame(1, $countingPdo->queryCount);
    assertSame(['Дриль', 'Навушники'], array_column($rows, 'name'));
    assertSame(['Інструменти', 'Електроніка'], array_column($rows, 'category_name'));
});

exit(summary());

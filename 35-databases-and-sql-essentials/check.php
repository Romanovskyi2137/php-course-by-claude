<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertSame, connectTestDatabase, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

$pdo = connectTestDatabase();
if ($pdo === null) {
    exit(summary());
}

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
$pdo->exec("INSERT INTO products (id, name, category_id, price, in_stock) VALUES
    (1, 'Дриль', 1, 45.00, 1),
    (2, 'Навушники', 2, 35.50, 1),
    (3, 'Клавіатура', 2, 25.00, 0)");

section('Вимога 1: select_in_stock_products_sql()');
test('повертає лише продукти в наявності, відсортовані за id', function () use ($pdo): void {
    $rows = $pdo->query(select_in_stock_products_sql())->fetchAll();
    assertSame([1, 2], array_map(static fn (array $r) => (int) $r['id'], $rows));
});

section('Вимога 2: select_products_with_category_sql()');
test('JOIN повертає id, product_name, category_name', function () use ($pdo): void {
    $rows = $pdo->query(select_products_with_category_sql())->fetchAll();
    assertSame(3, count($rows));
    assertSame(['id', 'product_name', 'category_name'], array_keys($rows[0]));
    assertSame('Дриль', $rows[0]['product_name']);
    assertSame('Інструменти', $rows[0]['category_name']);
    assertSame('Навушники', $rows[1]['product_name']);
    assertSame('Електроніка', $rows[1]['category_name']);
});

section('Вимога 3: insert_product_sql()');
test('вставляє новий продукт з переданими значеннями', function () use ($pdo): void {
    $stmt = $pdo->prepare(insert_product_sql());
    $stmt->execute(['Монітор', 2, 150.00, 1]);
    $newId = (int) $pdo->lastInsertId();

    $check = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $check->execute([$newId]);
    $row = $check->fetch();

    assertSame('Монітор', $row['name']);
    assertSame(2, (int) $row['category_id']);
    assertEquals(150.00, $row['price']);
});

section('Вимога 4: update_price_sql()');
test('оновлює саме ціну вказаного продукту', function () use ($pdo): void {
    $stmt = $pdo->prepare(update_price_sql());
    $stmt->execute([40.00, 1]);

    $check = $pdo->prepare('SELECT price FROM products WHERE id = ?');
    $check->execute([1]);
    assertEquals(40.00, $check->fetchColumn());

    $unchanged = $pdo->prepare('SELECT price FROM products WHERE id = ?');
    $unchanged->execute([2]);
    assertEquals(35.50, $unchanged->fetchColumn());
});

exit(summary());

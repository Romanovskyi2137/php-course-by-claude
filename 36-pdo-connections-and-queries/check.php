<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertNull, assertSame, assertTrue, connectTestDatabase, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

$setupPdo = connectTestDatabase();
if ($setupPdo === null) {
    exit(summary());
}

$setupPdo->exec('DROP TABLE IF EXISTS products');
$setupPdo->exec('DROP TABLE IF EXISTS categories');
$setupPdo->exec('CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL)');
$setupPdo->exec('CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
)');
$setupPdo->exec("INSERT INTO categories (id, name) VALUES (1, 'Інструменти'), (2, 'Електроніка')");
$setupPdo->exec("INSERT INTO products (id, name, category_id, price, in_stock) VALUES
    (1, 'Дриль', 1, 45.00, 1),
    (2, 'Навушники', 2, 35.50, 1)");

section('Вимога 1: create_connection()');
$config = (require __DIR__ . '/../_env/config.local.php')['db'];
$pdo = null;
test('повертає робоче PDO-з\'єднання з ERRMODE_EXCEPTION', function () use ($config, &$pdo): void {
    $pdo = create_connection($config);
    assertTrue($pdo instanceof \PDO);
    assertSame(\PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
});

if ($pdo === null) {
    $pdo = $setupPdo; // якщо вимога 1 ще не реалізована — не валимо решту вимог поспіль
}

section('Вимога 2: fetch_all_products()');
test('повертає всі продукти, відсортовані за id', function () use ($pdo): void {
    $rows = fetch_all_products($pdo);
    assertSame([1, 2], array_map(static fn (array $r) => (int) $r['id'], $rows));
});

section('Вимога 3: ProductRow / fetch_product_as_object()');
test('повертає ProductRow із заповненими властивостями', function () use ($pdo): void {
    $product = fetch_product_as_object($pdo, 1);
    assertTrue($product instanceof ProductRow);
    assertSame(1, $product->id);
    assertSame('Дриль', $product->name);
    assertEquals(45.00, $product->price);
});
test('null для неіснуючого id', function () use ($pdo): void {
    assertNull(fetch_product_as_object($pdo, 999999));
});

section('Вимога 4: insert_product()');
test('вставляє продукт і повертає його id', function () use ($pdo): void {
    $newId = insert_product($pdo, 'Монітор', 150.00);
    assertTrue($newId > 0);

    $product = fetch_product_as_object($pdo, $newId);
    assertSame('Монітор', $product->name);
    assertEquals(150.00, $product->price);
});

exit(summary());

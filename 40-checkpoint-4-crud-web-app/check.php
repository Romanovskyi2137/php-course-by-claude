<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertNull, assertSame, assertTrue, connectTestDatabase, requireComposerAutoload, section, summary, test};
use App\Migrator;
use App\Product;
use App\ProductRepository;

$target = getenv('CHECK_TARGET') ?: 'starter';

if (!requireComposerAutoload(__DIR__ . "/{$target}/vendor/autoload.php")) {
    exit(summary());
}

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

section('Вимога 1: Product');
test('властивості доступні через promotion', function (): void {
    $product = new Product(1, 'Дриль', 1, 45.0, true);
    assertSame(1, $product->id);
    assertSame('Дриль', $product->name);
    assertSame(1, $product->categoryId);
    assertSame(45.0, $product->price);
    assertSame(true, $product->inStock);
});

section('Вимога 2: ProductRepository');
$repo = new ProductRepository($pdo);

test('create() вставляє рядок і повертає новий id', function () use ($repo): void {
    $id = $repo->create('Дриль', 1, 45.0, true);
    assertTrue($id > 0);
});
test('findById() повертає Product з правильно приведеними типами', function () use ($repo): void {
    $id = $repo->create('Навушники', 2, 35.5, true);
    $product = $repo->findById($id);
    assertTrue($product instanceof Product);
    assertSame('Навушники', $product->name);
    assertEquals(35.5, $product->price);
    assertSame(true, $product->inStock);
});
test('findById() повертає null для неіснуючого id', function () use ($repo): void {
    assertNull($repo->findById(999999));
});
test('findAll() повертає всі продукти, відсортовані за id', function () use ($pdo): void {
    $pdo->exec('DELETE FROM products');
    $repo = new ProductRepository($pdo);
    $repo->create('A', 1, 1.0, true);
    $repo->create('B', 1, 2.0, false);
    $rows = $repo->findAll();
    assertSame(['A', 'B'], array_map(fn (Product $p) => $p->name, $rows));
});
test('update() змінює рядок і повертає true', function () use ($pdo): void {
    $repo = new ProductRepository($pdo);
    $id = $repo->create('Стара назва', 1, 10.0, true);
    $updated = $repo->update($id, 'Нова назва', 2, 20.0, false);
    assertSame(true, $updated);
    $product = $repo->findById($id);
    assertSame('Нова назва', $product->name);
    assertSame(2, $product->categoryId);
    assertEquals(20.0, $product->price);
    assertSame(false, $product->inStock);
});
test('update() для неіснуючого id повертає false', function () use ($repo): void {
    assertSame(false, $repo->update(999999, 'X', 1, 1.0, true));
});
test('delete() видаляє рядок і повертає true', function () use ($pdo): void {
    $repo = new ProductRepository($pdo);
    $id = $repo->create('Тимчасовий', 1, 5.0, true);
    assertSame(true, $repo->delete($id));
    assertNull($repo->findById($id));
});
test('delete() для неіснуючого id повертає false', function () use ($repo): void {
    assertSame(false, $repo->delete(999999));
});

section('Вимога 3: Migrator::pending()');
test('повертає файли, яких немає серед застосованих, відсортовані', function (): void {
    $all = ['0002_create_products_table.sql', '0001_create_categories_table.sql', '0003_seed_categories.sql'];
    $applied = ['0001_create_categories_table.sql'];
    assertSame(
        ['0002_create_products_table.sql', '0003_seed_categories.sql'],
        Migrator::pending($all, $applied)
    );
});
test('порожній список, якщо все вже застосовано', function (): void {
    $all = ['0001_a.sql', '0002_b.sql'];
    assertSame([], Migrator::pending($all, $all));
});

exit(summary());

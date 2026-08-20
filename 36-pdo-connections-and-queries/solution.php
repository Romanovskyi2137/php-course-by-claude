<?php

declare(strict_types=1);

// Тема 36: PDO — з'єднання та запити — референсний розв'язок.

function create_connection(array $config): \PDO
{
    $dsn = "mysql:host={$config['host']};port={$config['port']};"
        . "dbname={$config['database']};charset={$config['charset']}";

    return new \PDO($dsn, $config['username'], $config['password'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);
}

function fetch_all_products(\PDO $pdo): array
{
    return $pdo->query('SELECT * FROM products ORDER BY id')->fetchAll();
}

class ProductRow
{
    public int $id;
    public string $name;
    public string $price;
}

function fetch_product_as_object(\PDO $pdo, int $id): ?ProductRow
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, ProductRow::class);

    $product = $stmt->fetch();

    return $product === false ? null : $product;
}

function insert_product(\PDO $pdo, string $name, float $price): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, 1, ?, 1)'
    );
    $stmt->execute([$name, $price]);

    return (int) $pdo->lastInsertId();
}

<?php

declare(strict_types=1);

// Тема 37: prepared statements, CRUD і транзакції — референсний розв'язок.

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

function update_price_by_id(\PDO $pdo, int $id, float $price): bool
{
    $stmt = $pdo->prepare('UPDATE products SET price = ? WHERE id = ?');
    $stmt->execute([$price, $id]);

    return $stmt->rowCount() > 0;
}

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

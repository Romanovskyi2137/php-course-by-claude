<?php

declare(strict_types=1);

namespace App;

// Checkpoint 4: CRUD-каталог — референсний розв'язок.

final class ProductRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM products ORDER BY id');

        return array_map($this->hydrate(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function create(string $name, int $categoryId, float $price, bool $inStock): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $categoryId, $price, $inStock ? 1 : 0]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, int $categoryId, float $price, bool $inStock): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET name = ?, category_id = ?, price = ?, in_stock = ? WHERE id = ?'
        );
        $stmt->execute([$name, $categoryId, $price, $inStock ? 1 : 0, $id]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    private function hydrate(array $row): Product
    {
        return new Product(
            id: (int) $row['id'],
            name: $row['name'],
            categoryId: (int) $row['category_id'],
            price: (float) $row['price'],
            inStock: (bool) $row['in_stock'],
        );
    }
}

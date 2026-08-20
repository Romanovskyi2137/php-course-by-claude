<?php

declare(strict_types=1);

namespace App;

// Checkpoint 4: CRUD-каталог. Опис вимог — у task.md.

// TODO (вимога 2): PDO-репозиторій продуктів — CRUD.
final class ProductRepository
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function findAll(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function findById(int $id): ?Product
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function create(string $name, int $categoryId, float $price, bool $inStock): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function update(int $id, string $name, int $categoryId, float $price, bool $inStock): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function delete(int $id): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

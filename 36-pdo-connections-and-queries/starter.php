<?php

declare(strict_types=1);

// Тема 36: PDO — з'єднання та запити. Опис завдання — у task.md.

// TODO (вимога 1): DSN + PDO з ERRMODE_EXCEPTION і FETCH_ASSOC.
function create_connection(array $config): \PDO
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): усі продукти, відсортовані за id.
function fetch_all_products(\PDO $pdo): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): типізований рядок результату для FETCH_CLASS.
class ProductRow
{
    public int $id;
    public string $name;
    public string $price;
}

// TODO (вимога 3): один продукт як ProductRow, null якщо не знайдено.
function fetch_product_as_object(\PDO $pdo, int $id): ?ProductRow
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): вставити продукт, повернути lastInsertId().
function insert_product(\PDO $pdo, string $name, float $price): int
{
    throw new \RuntimeException('Не реалізовано');
}

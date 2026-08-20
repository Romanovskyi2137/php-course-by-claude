<?php

declare(strict_types=1);

// Тема 37: prepared statements, CRUD і транзакції. Опис завдання — у task.md.

// TODO (вимога 1): вставити список продуктів в одній транзакції, повернути id.
function insert_products_batch(\PDO $pdo, array $products): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): оновити ціну за id, true якщо рядок знайдено й змінено.
function update_price_by_id(\PDO $pdo, int $id, float $price): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): сторінка продуктів через LIMIT/OFFSET.
function fetch_products_page(\PDO $pdo, int $page, int $perPage): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): усі продукти з категоріями одним запитом (без N+1).
function fetch_products_with_categories(\PDO $pdo): array
{
    throw new \RuntimeException('Не реалізовано');
}

<?php

declare(strict_types=1);

// Тема 35: бази даних та основи SQL — референсний розв'язок.

function select_in_stock_products_sql(): string
{
    return 'SELECT * FROM products WHERE in_stock = 1 ORDER BY id';
}

function select_products_with_category_sql(): string
{
    return 'SELECT p.id, p.name AS product_name, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.id';
}

function insert_product_sql(): string
{
    return 'INSERT INTO products (name, category_id, price, in_stock) VALUES (?, ?, ?, ?)';
}

function update_price_sql(): string
{
    return 'UPDATE products SET price = ? WHERE id = ?';
}

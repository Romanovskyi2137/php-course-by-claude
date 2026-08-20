<?php

declare(strict_types=1);

// Checkpoint 4: обробка форми додавання (PRG-патерн). Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

productRepository()->create(
    name: trim((string) ($_POST['name'] ?? '')),
    categoryId: (int) ($_POST['category_id'] ?? 0),
    price: (float) ($_POST['price'] ?? 0),
    inStock: isset($_POST['in_stock']),
);

header('Location: /');

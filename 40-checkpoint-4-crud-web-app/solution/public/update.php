<?php

declare(strict_types=1);

// Checkpoint 4: обробка форми редагування (PRG-патерн). Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

productRepository()->update(
    id: (int) ($_POST['id'] ?? 0),
    name: trim((string) ($_POST['name'] ?? '')),
    categoryId: (int) ($_POST['category_id'] ?? 0),
    price: (float) ($_POST['price'] ?? 0),
    inStock: isset($_POST['in_stock']),
);

header('Location: /');

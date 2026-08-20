<?php

declare(strict_types=1);

// Checkpoint 4: видалення продукту (PRG-патерн). Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

productRepository()->delete((int) ($_POST['id'] ?? 0));

header('Location: /');

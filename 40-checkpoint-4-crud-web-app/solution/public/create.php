<?php

declare(strict_types=1);

// Checkpoint 4: форма додавання продукту. Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

$cats = categories();
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Додати продукт</title></head>
<body>
    <h1>Додати продукт</h1>
    <form method="post" action="/store.php">
        <p><label>Назва: <input type="text" name="name" required></label></p>
        <p><label>Категорія:
            <select name="category_id">
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>"><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label></p>
        <p><label>Ціна: <input type="text" name="price" value="0.00" required></label></p>
        <p><label><input type="checkbox" name="in_stock" value="1" checked> В наявності</label></p>
        <button type="submit">Зберегти</button>
    </form>
    <p><a href="/">Назад до каталогу</a></p>
</body>
</html>

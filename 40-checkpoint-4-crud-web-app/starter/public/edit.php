<?php

declare(strict_types=1);

// Checkpoint 4: форма редагування продукту. Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

$product = productRepository()->findById((int) ($_GET['id'] ?? 0));
if ($product === null) {
    http_response_code(404);
    exit('Продукт не знайдено.');
}

$cats = categories();
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Редагувати продукт</title></head>
<body>
    <h1>Редагувати продукт #<?= $product->id ?></h1>
    <form method="post" action="/update.php">
        <input type="hidden" name="id" value="<?= $product->id ?>">
        <p><label>Назва: <input type="text" name="name" value="<?= e($product->name) ?>" required></label></p>
        <p><label>Категорія:
            <select name="category_id">
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (int) $cat['id'] === $product->categoryId ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label></p>
        <p><label>Ціна: <input type="text" name="price" value="<?= number_format($product->price, 2, '.', '') ?>" required></label></p>
        <p><label><input type="checkbox" name="in_stock" value="1" <?= $product->inStock ? 'checked' : '' ?>> В наявності</label></p>
        <button type="submit">Зберегти</button>
    </form>
    <p><a href="/">Назад до каталогу</a></p>
</body>
</html>

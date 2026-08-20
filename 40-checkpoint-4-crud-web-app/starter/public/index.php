<?php

declare(strict_types=1);

// Checkpoint 4: список продуктів. Готовий код — не редагуй.

require __DIR__ . '/_bootstrap.php';

use App\Product;

$products = productRepository()->findAll();
$categoryNames = array_column(categories(), 'name', 'id');

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Каталог продуктів</title></head>
<body>
    <h1>Каталог продуктів</h1>
    <p><a href="/create.php">+ Додати продукт</a></p>

    <?php if ($products === []): ?>
        <p>Продуктів ще немає.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>ID</th><th>Назва</th><th>Категорія</th><th>Ціна</th><th>В наявності</th><th></th></tr>
            <?php foreach ($products as $product): /** @var Product $product */ ?>
                <tr>
                    <td><?= $product->id ?></td>
                    <td><?= e($product->name) ?></td>
                    <td><?= e($categoryNames[$product->categoryId] ?? '?') ?></td>
                    <td><?= number_format($product->price, 2) ?></td>
                    <td><?= $product->inStock ? 'так' : 'ні' ?></td>
                    <td>
                        <a href="/edit.php?id=<?= $product->id ?>">редагувати</a>
                        <form method="post" action="/delete.php" style="display:inline">
                            <input type="hidden" name="id" value="<?= $product->id ?>">
                            <button type="submit">видалити</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>

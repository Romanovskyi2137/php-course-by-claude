<?php

declare(strict_types=1);

// Тема 30: реальна форма реєстрації з валідацією й завантаженням файлу.
// Запусти: php -S localhost:8000 -t public
// Готовий код — не редагуй. Логіка живе в ../starter.php.

require __DIR__ . '/../starter.php';

header('Content-Type: text/html; charset=utf-8');

$old = [];
if (isset($_GET['old'])) {
    $decoded = json_decode((string) $_GET['old'], true);
    $old = is_array($decoded) ? $decoded : [];
}
$errors = [];
if (isset($_GET['errors'])) {
    $decoded = json_decode((string) $_GET['errors'], true);
    $errors = is_array($decoded) ? $decoded : [];
}
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Тема 30: реєстрація</title></head>
<body>
    <h1>Реєстрація</h1>

    <?php if ($errors !== []): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $field => $message): ?>
                <li><?= htmlspecialchars($field) ?>: <?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="/submit.php" enctype="multipart/form-data">
        <p>
            <label>Email: <input type="text" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"></label>
        </p>
        <p>
            <label>Пароль: <input type="password" name="password"></label>
        </p>
        <p>
            <label>Вік: <input type="text" name="age" value="<?= htmlspecialchars($old['age'] ?? '') ?>"></label>
        </p>
        <p>
            <label>Аватар (необов'язково): <input type="file" name="avatar"></label>
        </p>
        <button type="submit">Зареєструватись</button>
    </form>
</body>
</html>

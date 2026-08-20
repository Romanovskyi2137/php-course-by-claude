<?php

declare(strict_types=1);

// Checkpoint 3: вхід адміна. Готовий код — не редагуй.

require __DIR__ . '/../src/classes.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

session_start();

$_SESSION['csrf_token'] ??= generate_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_SESSION['csrf_token'] ?? null, $_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('Некоректний CSRF-токен.');
    }

    $admin = require __DIR__ . '/../../data/admin.php';

    if (attempt_admin_login((string) ($_POST['password'] ?? ''), $admin['passwordHash'])) {
        session_regenerate_id(true); // новий ID при зміні рівня привілеїв
        $_SESSION['admin'] = true;
        header('Location: /index.php');
        exit;
    }

    $error = 'Невірний пароль.';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Вхід адміна</title></head>
<body>
    <h1>Вхід адміна</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" action="/login.php">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <p><label>Пароль: <input type="password" name="password"></label></p>
        <button type="submit">Увійти</button>
    </form>
    <p><a href="/index.php">Назад до гостьової книги</a></p>
</body>
</html>

<?php

declare(strict_types=1);

// Тема 31: реальна сесія — лічильник відвідувань + вхід/вихід.
// Запусти: php -S localhost:8000 -t public
// Готовий код — не редагуй. Логіка живе в ../starter.php.

require __DIR__ . '/../starter.php';

session_set_cookie_params(build_session_cookie_options(secure: false)); // secure: false — локальна розробка без HTTPS
session_start();

$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;

header('Content-Type: text/html; charset=utf-8');
$loggedIn = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Тема 31: сесії</title></head>
<body>
    <h1>Демо сесій</h1>
    <p>Session ID: <code><?= htmlspecialchars(session_id()) ?></code></p>
    <p>Візитів у цій сесії: <strong><?= (int) $_SESSION['visits'] ?></strong> (онови сторінку — зростатиме)</p>

    <?php if ($loggedIn): ?>
        <p>Ти залогинений як user #<?= (int) $_SESSION['user_id'] ?>.</p>
        <form method="post" action="/logout.php"><button type="submit">Вийти</button></form>
    <?php else: ?>
        <p>Ти анонім.</p>
        <form method="post" action="/login.php">
            <label>User ID: <input type="text" name="user_id" value="42"></label>
            <button type="submit">Увійти</button>
        </form>
    <?php endif; ?>

    <p><a href="/">оновити сторінку</a></p>
</body>
</html>

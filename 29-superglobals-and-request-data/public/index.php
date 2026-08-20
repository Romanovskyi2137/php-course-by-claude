<?php

declare(strict_types=1);

// Тема 29: реальна демонстрація роботи з даними запиту.
// Запусти: php -S localhost:8000 -t public
// Спробуй: http://localhost:8000/?q=php&page=2&secret=ignored
// Готовий код — не редагуй. Логіка живе в ../starter.php.

require __DIR__ . '/../starter.php';

header('Content-Type: text/html; charset=utf-8');

$allowed = ['q', 'page'];
$safeParams = sanitize_get_params($_GET, $allowed);
$ip = client_ip($_SERVER);
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Тема 29: суперглобали</title></head>
<body>
    <h1>Дані цього запиту</h1>

    <h2>$_GET (сирий, як прийшов)</h2>
    <pre><?= htmlspecialchars(print_r($_GET, true)) ?></pre>

    <h2>sanitize_get_params($_GET, ['q', 'page'])</h2>
    <pre><?= htmlspecialchars(print_r($safeParams, true)) ?></pre>
    <p>Зверни увагу: будь-який інший GET-параметр (наприклад <code>secret</code>)
        сюди не потрапить, навіть якщо він є в URL.</p>

    <h2>client_ip($_SERVER)</h2>
    <p><?= htmlspecialchars($ip) ?></p>

    <p>
        <a href="/?q=php&page=2&secret=ignored">спробувати з query-параметрами</a>
    </p>
</body>
</html>

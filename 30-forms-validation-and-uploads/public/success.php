<?php

declare(strict_types=1);

// Тема 30: сторінка успіху після PRG-редіректу. Готовий код — не редагуй.

header('Content-Type: text/html; charset=utf-8');
$upload = htmlspecialchars($_GET['upload'] ?? 'без файлу');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Тема 30: успіх</title></head>
<body>
    <h1>Реєстрація успішна</h1>
    <p>Аватар: <?= $upload ?></p>
    <p>Онови сторінку (F5) — переконайся, що форма НЕ відправляється повторно
        (це і є PRG: ти зараз на GET-сторінці, а не повторюєш POST).</p>
    <p><a href="/">Назад до форми</a></p>
</body>
</html>

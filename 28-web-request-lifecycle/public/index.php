<?php

declare(strict_types=1);

// Тема 28: реальна демонстрація життєвого циклу запиту.
// Запусти: php -S localhost:8000 -t public
// Готовий код — не редагуй. Логіка живе в ../starter.php.

require __DIR__ . '/../starter.php';

header('Content-Type: text/html; charset=utf-8');

$requestLine = sprintf(
    '%s %s %s',
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1'
);
$parsed = parse_request_line($requestLine);

$exampleQuery = build_query_string(['category' => 'Електроніка', 'page' => 2]);
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Тема 28: життєвий цикл запиту</title></head>
<body>
    <h1>Цей запит, розібраний через parse_request_line()</h1>
    <ul>
        <li>Метод: <strong><?= htmlspecialchars($parsed['method']) ?></strong></li>
        <li>Шлях: <strong><?= htmlspecialchars($parsed['path']) ?></strong></li>
        <li>Query: <strong><?= htmlspecialchars($parsed['query'] ?? '(немає)') ?></strong></li>
        <li>Протокол: <strong>HTTP/<?= htmlspecialchars($parsed['protocolVersion']) ?></strong></li>
    </ul>

    <h2>Приклад build_query_string()</h2>
    <p>
        <code><?= htmlspecialchars($exampleQuery) ?></code> —
        <a href="/?<?= htmlspecialchars($exampleQuery) ?>">спробуй перейти за цим посиланням</a>
        і подивись, як зміниться Query вище.
    </p>

    <p>SAPI поточного процесу: <strong><?= htmlspecialchars(PHP_SAPI) ?></strong>
        (<code>cli-server</code> — це вбудований сервер; при <code>php check.php</code>
        у терміналі SAPI буде <code>cli</code>).</p>
</body>
</html>

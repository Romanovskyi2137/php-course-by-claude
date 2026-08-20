<?php

declare(strict_types=1);

// Checkpoint 3: видалення запису — лише для адміна. Готовий код — не редагуй.

require __DIR__ . '/../src/classes.php';

session_start();

if (($_SESSION['admin'] ?? false) !== true) {
    http_response_code(403);
    exit('Лише адмін може видаляти записи.');
}

if (!verify_csrf_token($_SESSION['csrf_token'] ?? null, $_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Некоректний CSRF-токен.');
}

$dataPath = __DIR__ . '/../../data/entries.json';
$repository = GuestbookRepository::fromJsonFile($dataPath);
$repository->deleteById((int) ($_POST['id'] ?? 0));
$repository->toJsonFile($dataPath);

header('Location: /index.php');

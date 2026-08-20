<?php

declare(strict_types=1);

// Checkpoint 3: обробка нового запису (PRG-патерн). Готовий код — не редагуй.

require __DIR__ . '/../src/classes.php';

function redirect_url_with_query(string $path, array $query): string
{
    return $path . '?' . http_build_query($query);
}

session_start();

if (!verify_csrf_token($_SESSION['csrf_token'] ?? null, $_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Некоректний CSRF-токен.');
}

$result = validate_entry($_POST);

if (!$result['valid']) {
    $old = ['name' => $_POST['name'] ?? '', 'message' => $_POST['message'] ?? ''];
    $url = redirect_url_with_query('/index.php', [
        'errors' => json_encode($result['errors'], JSON_THROW_ON_ERROR),
        'old' => json_encode($old, JSON_THROW_ON_ERROR),
    ]);
    header("Location: {$url}");
    exit;
}

$dataPath = __DIR__ . '/../../data/entries.json';
$repository = GuestbookRepository::fromJsonFile($dataPath);
$repository->add(new GuestbookEntry(
    id: $repository->nextId(),
    name: trim($_POST['name']),
    message: trim($_POST['message']),
    createdAt: date('Y-m-d H:i:s'),
));
$repository->toJsonFile($dataPath);

header('Location: /index.php');

<?php

declare(strict_types=1);

// Checkpoint 3: гостьова книга — головна сторінка. Готовий код — не редагуй.
// Логіка живе в ../src/classes.php.

require __DIR__ . '/../src/classes.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

session_start();

$_SESSION['csrf_token'] ??= generate_csrf_token();
$csrfToken = $_SESSION['csrf_token'];
$isAdmin = $_SESSION['admin'] ?? false;

$repository = GuestbookRepository::fromJsonFile(__DIR__ . '/../../data/entries.json');

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

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="uk">
<head><meta charset="utf-8"><title>Гостьова книга</title></head>
<body>
    <h1>Гостьова книга</h1>

    <?php if ($isAdmin): ?>
        <p>Ти залогинений як адмін.
            <form method="post" action="/logout.php" style="display:inline">
                <button type="submit">Вийти</button>
            </form>
        </p>
    <?php else: ?>
        <p><a href="/login.php">Вхід для адміна</a></p>
    <?php endif; ?>

    <h2>Залишити запис</h2>
    <?php if ($errors !== []): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $field => $message): ?>
                <li><?= e($field) ?>: <?= e($message) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" action="/submit.php">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <p><label>Ім'я: <input type="text" name="name" value="<?= e($old['name'] ?? '') ?>" maxlength="50"></label></p>
        <p><label>Повідомлення:<br><textarea name="message" maxlength="500"><?= e($old['message'] ?? '') ?></textarea></label></p>
        <button type="submit">Залишити запис</button>
    </form>

    <h2>Записи</h2>
    <ul>
        <?php foreach ($repository->all() as $entry): ?>
            <li>
                <strong><?= e($entry->name) ?></strong>
                (<?= e($entry->createdAt) ?>):
                <?= e($entry->message) ?>
                <?php if ($isAdmin): ?>
                    <form method="post" action="/delete.php" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="id" value="<?= (int) $entry->id ?>">
                        <button type="submit">Видалити</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

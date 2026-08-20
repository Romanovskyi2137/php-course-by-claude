<?php

declare(strict_types=1);

// Тема 30: обробник форми — PRG-патерн. Готовий код — не редагуй.
// Логіка живе в ../starter.php.

require __DIR__ . '/../starter.php';

$result = validate_registration($_POST);

if (!$result['valid']) {
    // Ніколи не клади пароль назад у query string при редіректі — лише "безпечні" поля.
    $old = ['email' => $_POST['email'] ?? '', 'age' => $_POST['age'] ?? ''];
    $url = redirect_url('/index.php', [
        'errors' => json_encode($result['errors'], JSON_THROW_ON_ERROR),
        'old' => json_encode($old, JSON_THROW_ON_ERROR),
    ]);
    header("Location: {$url}");
    exit;
}

$uploadInfo = 'без файлу';
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed = is_allowed_upload($_FILES['avatar'], ['image/png', 'image/jpeg', 'image/gif']);
    $uploadInfo = $allowed
        ? 'файл прийнято, ' . human_readable_size((int) $_FILES['avatar']['size'])
        : 'файл відхилено (недозволений тип або помилка завантаження)';
}

$url = redirect_url('/success.php', ['upload' => $uploadInfo]);
header("Location: {$url}");

<?php

declare(strict_types=1);

// Тема 30: форми, валідація та завантаження файлів — референсний розв'язок.

function validate_registration(array $input): array
{
    $errors = [];

    if (filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Некоректний email';
    }

    if (strlen($input['password'] ?? '') < 8) {
        $errors['password'] = 'Пароль має бути не коротшим за 8 символів';
    }

    $age = filter_var($input['age'] ?? '', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 13, 'max_range' => 120],
    ]);
    if ($age === false) {
        $errors['age'] = 'Вік має бути числом від 13 до 120';
    }

    return ['valid' => $errors === [], 'errors' => $errors];
}

function redirect_url(string $path, array $query = []): string
{
    if ($query === []) {
        return $path;
    }

    return $path . '?' . http_build_query($query);
}

function is_allowed_upload(array $file, array $allowedMimeTypes): bool
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $realType = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    return in_array($realType, $allowedMimeTypes, strict: true);
}

function human_readable_size(int $bytes): string
{
    if ($bytes < 1024) {
        return "{$bytes} B";
    }

    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }

    return round($bytes / (1024 * 1024), 1) . ' MB';
}

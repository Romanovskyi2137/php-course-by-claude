<?php

declare(strict_types=1);

// Тема 30: форми, валідація та завантаження файлів. Опис завдання — у task.md.

// TODO (вимога 1): валідувати email/password/age, зібрати помилки.
function validate_registration(array $input): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): URL для PRG-редіректу.
function redirect_url(string $path, array $query = []): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): перевірити завантажений файл за реальним MIME-типом (finfo).
function is_allowed_upload(array $file, array $allowedMimeTypes): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): розмір у байтах -> "N B"/"N KB"/"N MB".
function human_readable_size(int $bytes): string
{
    throw new \RuntimeException('Не реалізовано');
}

<?php

declare(strict_types=1);

// Тема 32: автентифікація та паролі. Опис завдання — у task.md.

// TODO (вимога 1): хешувати пароль через password_hash(PASSWORD_DEFAULT).
function hash_password(string $plain): string
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): знайти користувача за email, перевірити пароль, не повертати хеш.
function attempt_login(array $users, string $email, string $password): ?array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): чи варто перехешувати з поточними параметрами.
function needs_rehash(string $hash): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): порівняння секретів за постійний час.
function constant_time_equals(string $a, string $b): bool
{
    throw new \RuntimeException('Не реалізовано');
}

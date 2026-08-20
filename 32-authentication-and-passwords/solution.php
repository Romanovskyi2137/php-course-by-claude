<?php

declare(strict_types=1);

// Тема 32: автентифікація та паролі — референсний розв'язок.

function hash_password(string $plain): string
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

function attempt_login(array $users, string $email, string $password): ?array
{
    foreach ($users as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            if (!password_verify($password, $user['passwordHash'])) {
                return null;
            }

            return ['id' => $user['id'], 'email' => $user['email']];
        }
    }

    return null;
}

function needs_rehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

function constant_time_equals(string $a, string $b): bool
{
    return hash_equals($a, $b);
}

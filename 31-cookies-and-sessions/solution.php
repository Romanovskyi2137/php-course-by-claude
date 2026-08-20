<?php

declare(strict_types=1);

// Тема 31: куки та сесії — референсний розв'язок.

function build_session_cookie_options(bool $secure): array
{
    return [
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure,
    ];
}

function is_session_expired(int $lastActivity, int $now, int $maxIdleSeconds): bool
{
    return ($now - $lastActivity) > $maxIdleSeconds;
}

function mark_authenticated(array $session, int $userId): array
{
    $session['user_id'] = $userId;
    $session['authenticated_at'] = true;

    return $session;
}

function logout(array $session): array
{
    unset($session['user_id'], $session['authenticated_at']);

    return $session;
}

<?php

declare(strict_types=1);

// Тема 31: куки та сесії. Опис завдання — у task.md.

// TODO (вимога 1): безпечні опції сесійної куки.
function build_session_cookie_options(bool $secure): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): чи минув таймаут неактивності.
function is_session_expired(int $lastActivity, int $now, int $maxIdleSeconds): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): позначити сесію автентифікованою, повернути новий масив.
function mark_authenticated(array $session, int $userId): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): очистити ознаки автентифікації, лишити решту ключів.
function logout(array $session): array
{
    throw new \RuntimeException('Не реалізовано');
}

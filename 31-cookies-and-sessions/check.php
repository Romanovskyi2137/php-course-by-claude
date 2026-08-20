<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: build_session_cookie_options()');
test('повертає httponly/samesite/secure відповідно до параметра', function (): void {
    assertSame([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => true,
    ], build_session_cookie_options(true));
});
test('secure=false, коли передано false (наприклад, локальна розробка без HTTPS)', function (): void {
    assertSame(false, build_session_cookie_options(false)['secure']);
});

section('Вимога 2: is_session_expired()');
test('true, якщо минуло більше maxIdleSeconds', function (): void {
    assertSame(true, is_session_expired(1000, 1000 + 1801, 1800));
});
test('false, якщо в межах maxIdleSeconds', function (): void {
    assertSame(false, is_session_expired(1000, 1000 + 1799, 1800));
});
test('false рівно на межі (не строго більше)', function (): void {
    assertSame(false, is_session_expired(1000, 1000 + 1800, 1800));
});

section('Вимога 3: mark_authenticated()');
test('додає user_id і authenticated_at, лишає наявні ключі', function (): void {
    $session = mark_authenticated(['theme' => 'dark'], 42);
    assertSame(['theme' => 'dark', 'user_id' => 42, 'authenticated_at' => true], $session);
});
test('не змінює переданий масив', function (): void {
    $original = ['theme' => 'dark'];
    mark_authenticated($original, 42);
    assertSame(['theme' => 'dark'], $original);
});

section('Вимога 4: logout()');
test('видаляє ознаки автентифікації, лишає решту', function (): void {
    $session = logout(['theme' => 'dark', 'user_id' => 42, 'authenticated_at' => true]);
    assertSame(['theme' => 'dark'], $session);
});
test('на сесії без автентифікації нічого не ламає', function (): void {
    assertSame(['theme' => 'dark'], logout(['theme' => 'dark']));
});

exit(summary());

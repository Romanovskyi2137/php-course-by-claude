<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: hash_password()');
test('повертає хеш, що верифікується через password_verify()', function (): void {
    $hash = hash_password('correct horse battery staple');
    assertSame(true, password_verify('correct horse battery staple', $hash));
});
test('той самий пароль дає різний хеш щоразу (різна сіль)', function (): void {
    assertSame(false, hash_password('same-password') === hash_password('same-password'));
});

section('Вимога 2: attempt_login()');
$users = [
    ['id' => 1, 'email' => 'alice@example.com', 'passwordHash' => password_hash('alicepass', PASSWORD_DEFAULT)],
    ['id' => 2, 'email' => 'Bob@Example.com', 'passwordHash' => password_hash('bobpass', PASSWORD_DEFAULT)],
];
test('успішний логін повертає id і email без passwordHash', function () use ($users): void {
    $result = attempt_login($users, 'alice@example.com', 'alicepass');
    assertSame(['id' => 1, 'email' => 'alice@example.com'], $result);
});
test('email порівнюється без урахування регістру', function () use ($users): void {
    $result = attempt_login($users, 'bob@example.com', 'bobpass');
    assertSame(2, $result['id']);
});
test('невірний пароль -> null', function () use ($users): void {
    assertNull(attempt_login($users, 'alice@example.com', 'wrong'));
});
test('невідомий email -> null', function () use ($users): void {
    assertNull(attempt_login($users, 'nobody@example.com', 'whatever'));
});

section('Вимога 3: needs_rehash()');
test('false для хеша, створеного з поточними параметрами за замовчуванням', function (): void {
    assertSame(false, needs_rehash(hash_password('anything')));
});
test('true для хеша зі слабшим cost, ніж типовий', function (): void {
    $weakHash = password_hash('anything', PASSWORD_BCRYPT, ['cost' => 4]);
    assertSame(true, needs_rehash($weakHash));
});

section('Вимога 4: constant_time_equals()');
test('true для однакових рядків', fn () => assertSame(true, constant_time_equals('token-abc', 'token-abc')));
test('false для різних рядків', fn () => assertSame(false, constant_time_equals('token-abc', 'token-xyz')));

exit(summary());

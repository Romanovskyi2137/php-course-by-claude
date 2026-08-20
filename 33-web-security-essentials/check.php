<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: escape_html()');
test('екранує теги й лапки', function (): void {
    assertSame(
        '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;',
        escape_html('<script>alert("x")</script>')
    );
});
test('екранує одинарні лапки (ENT_QUOTES)', function (): void {
    assertSame('&#039;quoted&#039;', escape_html("'quoted'"));
});

section('Вимога 2: generate_csrf_token()');
test('повертає 64 hex-символи', function (): void {
    $token = generate_csrf_token();
    assertSame(64, strlen($token));
    assertSame(1, preg_match('/^[0-9a-f]{64}$/', $token));
});
test('кожен виклик дає інший токен', function (): void {
    assertSame(false, generate_csrf_token() === generate_csrf_token());
});

section('Вимога 3: verify_csrf_token()');
test('true для однакових токенів', function (): void {
    assertSame(true, verify_csrf_token('abc123', 'abc123'));
});
test('false для різних токенів', function (): void {
    assertSame(false, verify_csrf_token('abc123', 'xyz789'));
});
test('false, якщо очікуваний токен null (немає в сесії)', function (): void {
    assertSame(false, verify_csrf_token(null, 'abc123'));
});
test('false, якщо надісланий токен null (форма без поля)', function (): void {
    assertSame(false, verify_csrf_token('abc123', null));
});

section('Вимога 4: filter_allowed_fields()');
test('лишає лише дозволені поля', function (): void {
    $result = filter_allowed_fields(
        ['name' => 'Іван', 'email' => 'user@example.com', 'is_admin' => '1'],
        ['name', 'email']
    );
    assertSame(['name' => 'Іван', 'email' => 'user@example.com'], $result);
});
test('не додає полів, яких немає у вхідних даних', function (): void {
    assertSame(['name' => 'Іван'], filter_allowed_fields(['name' => 'Іван'], ['name', 'bio']));
});

section('Вимога 5: content_security_policy_header()');
test('будує значення заголовка з кількох директив', function (): void {
    assertSame(
        "default-src 'self'; script-src 'self'",
        content_security_policy_header(['default-src' => "'self'", 'script-src' => "'self'"])
    );
});
test('одна директива без зайвого роздільника', function (): void {
    assertSame("default-src 'none'", content_security_policy_header(['default-src' => "'none'"]));
});

exit(summary());

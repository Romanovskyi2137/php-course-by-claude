<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: client_ip()');
test('бере перший IP з X-Forwarded-For', function (): void {
    assertSame('203.0.113.5', client_ip([
        'HTTP_X_FORWARDED_FOR' => '203.0.113.5, 10.0.0.1',
        'REMOTE_ADDR' => '10.0.0.1',
    ]));
});
test('без X-Forwarded-For бере REMOTE_ADDR', function (): void {
    assertSame('192.168.1.1', client_ip(['REMOTE_ADDR' => '192.168.1.1']));
});
test('без жодного з ключів повертає unknown', fn () => assertSame('unknown', client_ip([])));

section('Вимога 2: is_json_request()');
test('true для application/json', function (): void {
    assertSame(true, is_json_request(['CONTENT_TYPE' => 'application/json']));
});
test('true для application/json з charset', function (): void {
    assertSame(true, is_json_request(['CONTENT_TYPE' => 'application/json; charset=utf-8']));
});
test('false для form-urlencoded', function (): void {
    assertSame(false, is_json_request(['CONTENT_TYPE' => 'application/x-www-form-urlencoded']));
});
test('false без CONTENT_TYPE', fn () => assertSame(false, is_json_request([])));

section('Вимога 3: parse_json_body()');
test('декодує коректне JSON-тіло', function (): void {
    assertSame(['name' => 'Іван', 'age' => 30], parse_json_body('{"name":"Іван","age":30}'));
});
test('кидає JsonException на некоректне тіло', function (): void {
    assertThrows(\JsonException::class, fn () => parse_json_body('{broken'));
});

section('Вимога 4: sanitize_get_params()');
test('лишає лише дозволені ключі', function (): void {
    $result = sanitize_get_params(
        ['q' => 'php', 'page' => '2', 'secret' => 'ignored'],
        ['q', 'page']
    );
    assertSame(['q' => 'php', 'page' => '2'], $result);
});
test('обрізає пробіли навколо значень', function (): void {
    assertSame(['q' => 'php'], sanitize_get_params(['q' => '  php  '], ['q']));
});
test('не додає ключів, яких немає у вхідному масиві, навіть якщо вони дозволені', function (): void {
    assertSame(['q' => 'php'], sanitize_get_params(['q' => 'php'], ['q', 'page']));
});

exit(summary());

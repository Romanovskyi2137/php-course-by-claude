<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: parse_request_line()');
test('розбирає метод/шлях/query/протокол', function (): void {
    assertSame([
        'method' => 'GET',
        'path' => '/users',
        'query' => 'id=5',
        'protocolVersion' => '1.1',
    ], parse_request_line('GET /users?id=5 HTTP/1.1'));
});
test('query дорівнює null, якщо в цілі запиту немає ?', function (): void {
    assertSame(null, parse_request_line('POST /login HTTP/1.1')['query']);
});

section('Вимога 2: reason_phrase()');
test('повертає стандартну фразу для відомого коду', function (): void {
    assertSame('OK', reason_phrase(200));
    assertSame('Not Found', reason_phrase(404));
    assertSame('Internal Server Error', reason_phrase(500));
});
test('кидає InvalidArgumentException для невідомого коду', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => reason_phrase(999));
});

section('Вимога 3: status_class()');
test('класифікує коди за першою цифрою', function (): void {
    assertSame('informational', status_class(101));
    assertSame('success', status_class(204));
    assertSame('redirect', status_class(301));
    assertSame('client_error', status_class(404));
    assertSame('server_error', status_class(503));
});
test('кидає InvalidArgumentException поза діапазоном 100-599', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => status_class(50));
    assertThrows(\InvalidArgumentException::class, fn () => status_class(600));
});

section('Вимога 4: build_query_string()');
test('будує query-рядок з асоціативного масиву', function (): void {
    assertSame('category=Tools&page=2', build_query_string(['category' => 'Tools', 'page' => 2]));
});
test('коректно кодує пробіли', function (): void {
    assertSame('q=hello+world', build_query_string(['q' => 'hello world']));
});

exit(summary());

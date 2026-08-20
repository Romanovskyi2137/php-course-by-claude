<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: extract_email_domain()');
test('дістає домен з коректного email', function (): void {
    assertSame('example.com', extract_email_domain('user@example.com'));
});
test('null для рядка без @', fn () => assertNull(extract_email_domain('not-an-email')));

section('Вимога 2: normalize_whitespace()');
test('схлопує пробіли/табуляції/переноси рядків в один пробіл', function (): void {
    assertSame(
        'Привіт, світ! як справи?',
        normalize_whitespace("  Привіт,\n\n  світ!\t\tяк   справи?  ")
    );
});
test('рядок без зайвих пробілів лишається без змін', function (): void {
    assertSame('a b c', normalize_whitespace('a b c'));
});

section('Вимога 3: parse_log_line()');
test('парсить коректний рядок логу', function (): void {
    $result = parse_log_line('[2026-08-20 10:15:00] ERROR: Щось пішло не так');
    assertSame([
        'date' => '2026-08-20 10:15:00',
        'level' => 'ERROR',
        'message' => 'Щось пішло не так',
    ], $result);
});
test('null для рядка, що не відповідає формату', fn () => assertNull(parse_log_line('просто текст')));

section('Вимога 4: split_ukrainian_words()');
test('розбиває кириличний текст на слова без розділових знаків', function (): void {
    assertSame(
        ['Привіт', 'світ', 'Як', 'справи'],
        split_ukrainian_words('Привіт, світ! Як справи?')
    );
});
test('порожній рядок дає порожній масив', fn () => assertSame([], split_ukrainian_words('   ...   ')));

exit(summary());

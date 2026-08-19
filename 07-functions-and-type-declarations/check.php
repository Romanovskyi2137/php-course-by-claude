<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertNull, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: format_name()');
test('без middle', fn () => assertSame('Ada Lovelace', format_name('Ada', 'Lovelace')));
test('з middle', fn () => assertSame('Ada King Lovelace', format_name('Ada', 'Lovelace', 'King')));

section('Вимога 2: try_parse_int()');
test("'42' -> 42", fn () => assertSame(42, try_parse_int('42')));
test("' 42 ' з пробілами -> 42", fn () => assertSame(42, try_parse_int(' 42 ')));
test("'abc' -> null", fn () => assertNull(try_parse_int('abc')));
test("'' -> null", fn () => assertNull(try_parse_int('')));
test("'12.5' -> null (не ціле)", fn () => assertNull(try_parse_int('12.5')));

section('Вимога 3: increment_by()');
test('за замовчуванням крок 1', function (): void {
    $x = 5;
    increment_by($x);
    assertSame(6, $x);
});
test('явний крок', function (): void {
    $x = 5;
    increment_by($x, 10);
    assertSame(15, $x);
});

section('Вимога 4: next_sequence_value()');
test('перший виклик повертає 1, наступні збільшуються', function (): void {
    $first = next_sequence_value();
    $second = next_sequence_value();
    $third = next_sequence_value();
    assertSame($first + 1, $second);
    assertSame($second + 1, $third);
});

exit(summary());

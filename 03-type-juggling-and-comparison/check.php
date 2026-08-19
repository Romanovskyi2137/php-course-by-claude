<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: is_truthy()');
test("'0' є falsy", fn () => assertSame(false, is_truthy('0')));
test("'0.0' є truthy (на відміну від '0')", fn () => assertSame(true, is_truthy('0.0')));
test('порожній масив falsy', fn () => assertSame(false, is_truthy([])));
test('0 falsy, 1 truthy', function (): void {
    assertSame(false, is_truthy(0));
    assertSame(true, is_truthy(1));
});

section('Вимога 2: strict_equals()');
test('0 === false це false (різні типи)', fn () => assertSame(false, strict_equals(0, false)));
test("1 === '1' це false (різні типи)", fn () => assertSame(false, strict_equals(1, '1')));
test('1 === 1 це true', fn () => assertSame(true, strict_equals(1, 1)));

section('Вимога 3: loose_equals()');
test('0 == false це true', fn () => assertSame(true, loose_equals(0, false)));
test("1 == '1' це true", fn () => assertSame(true, loose_equals(1, '1')));
test("PHP8: 0 == 'foo' це false (число приводиться до рядка)", function (): void {
    assertSame(false, loose_equals(0, 'foo'));
});

section('Вимога 4: key_status()');
test('відсутній ключ -> missing', function (): void {
    assertSame('missing', key_status(['a' => 1], 'z'));
});
test('ключ зі значенням null -> null', function (): void {
    assertSame('null', key_status(['a' => null], 'a'));
});
test('ключ зі значенням 0 -> present (0 це не null)', function (): void {
    assertSame('present', key_status(['a' => 0], 'a'));
});

section('Вимога 5: compare()');
test('менше -> -1', fn () => assertSame(-1, compare(1, 2)));
test('рівні -> 0', fn () => assertSame(0, compare(2, 2)));
test('більше -> 1', fn () => assertSame(1, compare(3, 2)));

exit(summary());

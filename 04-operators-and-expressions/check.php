<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: integer_divide()');
test('7 поділити на 2 -> 3', fn () => assertSame(3, integer_divide(7, 2)));
test('-7 поділити на 2 -> -3 (округлення до нуля)', fn () => assertSame(-3, integer_divide(-7, 2)));

section('Вимога 2: coalesce()');
test('null -> default', fn () => assertSame(10, coalesce(null, 10)));
test('0 залишається 0 (0 не null)', fn () => assertSame(0, coalesce(0, 10)));
test("'' залишається '' ('' не null)", fn () => assertSame('', coalesce('', 'default')));

section('Вимога 3: elvis()');
test('0 -> default (0 falsy)', fn () => assertSame(10, elvis(0, 10)));
test("'' -> default ('' falsy)", fn () => assertSame('default', elvis('', 'default')));
test('непорожній рядок залишається', fn () => assertSame('hi', elvis('hi', 'default')));
test('coalesce і elvis дають РІЗНИЙ результат для 0', function (): void {
    assertSame(0, coalesce(0, 10));
    assertSame(10, elvis(0, 10));
});

section('Вимога 4: clamp()');
test('значення в межах діапазону не змінюється', fn () => assertSame(5, clamp(5, 0, 10)));
test('значення нижче мінімуму обрізається до мінімуму', fn () => assertSame(0, clamp(-5, 0, 10)));
test('значення вище максимуму обрізається до максимуму', fn () => assertSame(10, clamp(15, 0, 10)));

exit(summary());

<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: make_multiplier()');
test('множить на переданий фактор', function (): void {
    $double = make_multiplier(2);
    assertTrue($double instanceof Closure);
    assertSame(10, $double(5));
});
test('різні фабрики незалежні', function (): void {
    $double = make_multiplier(2);
    $triple = make_multiplier(3);
    assertSame(4, $double(2));
    assertSame(6, $triple(2));
});

section('Вимога 2: make_counter()');
test('повертає послідовні числа з 1', function (): void {
    $counter = make_counter();
    assertSame(1, $counter());
    assertSame(2, $counter());
    assertSame(3, $counter());
});
test('незалежні лічильники не впливають один на одного', function (): void {
    $a = make_counter();
    $b = make_counter();
    assertSame(1, $a());
    assertSame(2, $a());
    assertSame(1, $b());
});

section('Вимога 3: sum_all()');
test('сумує кілька чисел', fn () => assertSame(6, sum_all(1, 2, 3)));
test('без аргументів -> 0', fn () => assertSame(0, sum_all()));
test('одне число', fn () => assertSame(42, sum_all(42)));

section('Вимога 4: string_length_callable()');
test('повертає Closure', function (): void {
    assertTrue(string_length_callable() instanceof Closure);
});
test('поводиться як strlen()', function (): void {
    $len = string_length_callable();
    assertSame(5, $len('hello'));
    assertSame(0, $len(''));
});

exit(summary());

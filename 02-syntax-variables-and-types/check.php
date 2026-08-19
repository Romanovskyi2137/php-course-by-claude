<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertStrictTypesDeclared, assertThrows, section, summary, test};

$targetFile = __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';
require $targetFile;

section('Вимога 0: declare(strict_types=1)');
test('файл починається з declare(strict_types=1)', function () use ($targetFile): void {
    assertStrictTypesDeclared($targetFile);
});

section('Вимога 1: константа APP_NAME');
test('APP_NAME визначена через const зі значенням "PHP Course"', function (): void {
    assertSame(true, defined('APP_NAME'));
    assertSame('PHP Course', APP_NAME);
});

section('Вимога 2: type_of()');
test('int', fn () => assertSame('int', type_of(42)));
test('float', fn () => assertSame('float', type_of(4.2)));
test('string', fn () => assertSame('string', type_of('hi')));
test('bool', fn () => assertSame('bool', type_of(true)));
test('array', fn () => assertSame('array', type_of([1, 2])));
test('null', fn () => assertSame('null', type_of(null)));

section('Вимога 3: to_debug_string()');
test('форматує рядок через var_export', function (): void {
    assertSame(var_export('hi', true), to_debug_string('hi'));
});
test('форматує масив через var_export', function (): void {
    assertSame(var_export([1, 'a' => 2], true), to_debug_string([1, 'a' => 2]));
});

section('Вимога 4: sum_strict()');
test('додає два цілих числа', fn () => assertSame(5, sum_strict(2, 3)));
test('кидає TypeError на float-аргумент (strict_types=1)', function (): void {
    assertThrows(\TypeError::class, fn () => sum_strict(2.0, 3));
});
test('кидає TypeError на числовий рядок (strict_types=1)', function (): void {
    /** @phpstan-ignore-next-line навмисно передаємо неправильний тип, щоб перевірити strict_types */
    assertThrows(\TypeError::class, fn () => sum_strict('2', 3));
});

exit(summary());

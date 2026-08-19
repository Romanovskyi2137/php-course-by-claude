<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: php_major_version()');
test('обчислює major-версію з PHP_VERSION_ID', function (): void {
    assertSame(intdiv(PHP_VERSION_ID, 10000), php_major_version());
});
test('повертає саме поточну major-версію (>= 8, курс на PHP 8.3+)', function (): void {
    assertTrue(php_major_version() >= 8, 'Курс розрахований на PHP 8.3+, перевір встановлену версію');
});

section('Вимога 2: is_at_least()');
test('true для версії, яка точно менша за поточну', function (): void {
    assertSame(true, is_at_least('7.0.0'));
});
test('false для версії, яка точно більша за поточну', function (): void {
    assertSame(false, is_at_least('99.0.0'));
});
test('true для точного співпадіння з поточною версією', function (): void {
    assertSame(true, is_at_least(PHP_VERSION));
});

section('Вимога 3: current_sapi()');
test('повертає реальний PHP_SAPI, а не хардкод', function (): void {
    assertSame(PHP_SAPI, current_sapi());
});

section('Вимога 4: format_environment_report()');
test('містить рядок з PHP version', function (): void {
    assertTrue(str_contains(format_environment_report(), 'PHP version: ' . PHP_VERSION));
});
test('містить рядок з SAPI', function (): void {
    assertTrue(str_contains(format_environment_report(), 'SAPI: ' . PHP_SAPI));
});
test('містить рядок з OS family', function (): void {
    assertTrue(str_contains(format_environment_report(), 'OS family: ' . PHP_OS_FAMILY));
});
test('містить рядок з кількістю розширень у форматі "Loaded extensions: N"', function (): void {
    assertTrue((bool) preg_match('/Loaded extensions: \d+/', format_environment_report()));
});
test('рядки розділені \n і їх рівно 4', function (): void {
    assertSame(4, count(explode("\n", format_environment_report())));
});

exit(summary());

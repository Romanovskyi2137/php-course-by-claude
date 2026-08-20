<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertFalse, assertSame, assertTrue, section, summary, test};

$target = getenv('CHECK_TARGET') ?: 'starter';
require __DIR__ . "/{$target}.php";

section('Вимога 1: psr4_file_path()');
test('мапить прості класи в базовій директорії', function (): void {
    assertSame('src/Money.php', psr4_file_path('App\\Money', 'App\\', 'src'));
});
test('мапить класи у вкладених неймспейсах у підпапки', function (): void {
    assertSame('src/Billing/Invoice.php', psr4_file_path('App\\Billing\\Invoice', 'App\\', 'src'));
});

section('Вимога 2: is_valid_psr1_class_name()');
test('true для StudlyCaps', function (): void {
    assertTrue(is_valid_psr1_class_name('ReportGenerator'));
    assertTrue(is_valid_psr1_class_name('Invoice2'));
});
test('false для snake_case чи camelCase', function (): void {
    assertFalse(is_valid_psr1_class_name('report_generator'));
    assertFalse(is_valid_psr1_class_name('reportGenerator'));
});

section('Вимога 3: is_valid_psr1_method_name()');
test('true для camelCase', function (): void {
    assertTrue(is_valid_psr1_method_name('generateSummary'));
    assertTrue(is_valid_psr1_method_name('toArray'));
});
test('false для snake_case чи StudlyCaps', function (): void {
    assertFalse(is_valid_psr1_method_name('generate_summary'));
    assertFalse(is_valid_psr1_method_name('GenerateSummary'));
});

section('Вимога 4: ReportGenerator');
test('клас перейменовано в ReportGenerator', function (): void {
    assertTrue(class_exists('ReportGenerator'));
});
test('generateSummary() рахує суму так само, як стара generate_summary()', function (): void {
    $report = new \ReportGenerator();
    assertSame('Total: 60', $report->generateSummary([10, 20, 30]));
});
test('generateSummary() коректно працює для порожнього масиву', function (): void {
    $report = new \ReportGenerator();
    assertSame('Total: 0', $report->generateSummary([]));
});
test('файл не містить табуляції (PSR-12: 4 пробіли)', function () use ($target): void {
    $source = file_get_contents(__DIR__ . "/{$target}.php");
    assertFalse(str_contains($source, "\t"));
});

exit(summary());

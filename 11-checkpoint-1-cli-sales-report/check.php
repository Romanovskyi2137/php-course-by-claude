<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '/src/functions.php';

section('Вимога 1: parse_filters()');
test('розбирає --category=... та --from=...', function (): void {
    $argv = ['bin/report.php', '--category=Електроніка', '--from=2026-01-01'];
    assertSame(['category' => 'Електроніка', 'from' => '2026-01-01'], parse_filters($argv));
});
test('без фільтрів -> порожній масив', function (): void {
    assertSame([], parse_filters(['bin/report.php']));
});
test('ігнорує аргументи без -- або без =', function (): void {
    $argv = ['bin/report.php', 'plain', '--flag-without-value'];
    assertSame([], parse_filters($argv));
});

section('Вимога 2: filter_sales()');
$sales = [
    ['date' => '2026-01-05', 'product' => 'A', 'category' => 'Tools', 'amount' => 10.0, 'qty' => 1],
    ['date' => '2026-01-15', 'product' => 'B', 'category' => 'Toys', 'amount' => 5.0, 'qty' => 2],
    ['date' => '2026-01-25', 'product' => 'C', 'category' => 'Tools', 'amount' => 20.0, 'qty' => 1],
];
test('фільтрує за category', function () use ($sales): void {
    $result = filter_sales($sales, ['category' => 'Tools']);
    assertSame(2, count($result));
    assertSame(['A', 'C'], array_column($result, 'product'));
});
test('фільтрує за діапазоном дат', function () use ($sales): void {
    $result = filter_sales($sales, ['from' => '2026-01-10', 'to' => '2026-01-20']);
    assertSame(['B'], array_column($result, 'product'));
});
test('без фільтрів повертає всі записи списком без дірок у ключах', function () use ($sales): void {
    $result = filter_sales($sales, []);
    assertSame([0, 1, 2], array_keys($result));
});
test('комбінація фільтрів', function () use ($sales): void {
    $result = filter_sales($sales, ['category' => 'Tools', 'from' => '2026-01-10']);
    assertSame(['C'], array_column($result, 'product'));
});

section('Вимога 3: group_totals_by_category()');
test('сумує amount * qty за категоріями', function () use ($sales): void {
    assertSame(['Tools' => 30.0, 'Toys' => 10.0], group_totals_by_category($sales));
});
test('порожній список -> порожній масив', fn () => assertSame([], group_totals_by_category([])));

section('Вимога 4: sorted_rows_by_total_desc()');
test('сортує за спаданням суми', function (): void {
    $rows = sorted_rows_by_total_desc(['A' => 10.0, 'B' => 30.0, 'C' => 20.0]);
    assertSame(['B', 'C', 'A'], array_column($rows, 'category'));
    assertSame([30.0, 20.0, 10.0], array_column($rows, 'total'));
});
test('при однаковій сумі сортує за категорією за зростанням', function (): void {
    $rows = sorted_rows_by_total_desc(['Zebra' => 10.0, 'Apple' => 10.0]);
    assertSame(['Apple', 'Zebra'], array_column($rows, 'category'));
});

section('Вимога 5: format_report_table()');
test('вирівнює латинські назви до 20 символів', function (): void {
    $table = format_report_table([['category' => 'Tools', 'total' => 30.0]]);
    assertSame('Tools' . str_repeat(' ', 15) . '     30.00', $table);
});
test('коректно вирівнює кириличні назви (mb_str_pad, не str_pad)', function (): void {
    $table = format_report_table([['category' => 'Тест', 'total' => 5.0]]);
    // 'Тест' — 4 символи, доповнено до 20 символів пробілами (НЕ байтів)
    assertSame('Тест' . str_repeat(' ', 16) . '      5.00', $table);
});
test('кілька рядків розділені \n', function (): void {
    $table = format_report_table([
        ['category' => 'A', 'total' => 1.0],
        ['category' => 'B', 'total' => 2.0],
    ]);
    assertSame(2, count(explode("\n", $table)));
});

exit(summary());

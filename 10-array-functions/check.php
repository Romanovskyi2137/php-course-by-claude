<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: active_usernames()');
test('лишає тільки активних, без дірок у ключах', function (): void {
    $users = [
        ['name' => 'Ada', 'active' => true],
        ['name' => 'Bob', 'active' => false],
        ['name' => 'Cid', 'active' => true],
    ];
    assertSame(['Ada', 'Cid'], active_usernames($users));
});
test('порожній список -> порожній масив', fn () => assertSame([], active_usernames([])));

section('Вимога 2: total_price()');
test('рахує суму price * qty', function (): void {
    $items = [
        ['price' => 10.0, 'qty' => 2],
        ['price' => 5.0, 'qty' => 3],
    ];
    assertSame(35.0, total_price($items));
});
test('порожній список -> 0.0', fn () => assertSame(0.0, total_price([])));

section('Вимога 3: sorted_by_score_desc()');
test('сортує за спаданням score', function (): void {
    $players = [
        ['name' => 'A', 'score' => 10],
        ['name' => 'B', 'score' => 30],
        ['name' => 'C', 'score' => 20],
    ];
    assertSame(['B', 'C', 'A'], array_column(sorted_by_score_desc($players), 'name'));
});
test('не змінює оригінальний масив', function (): void {
    $players = [['name' => 'A', 'score' => 1], ['name' => 'B', 'score' => 2]];
    sorted_by_score_desc($players);
    assertSame(['A', 'B'], array_column($players, 'name'));
});

section('Вимога 4: index_by_id()');
test('індексує записи за id', function (): void {
    $records = [
        ['id' => 1, 'name' => 'Ada'],
        ['id' => 2, 'name' => 'Alan'],
    ];
    $indexed = index_by_id($records);
    assertSame(['id' => 1, 'name' => 'Ada'], $indexed[1]);
    assertSame(['id' => 2, 'name' => 'Alan'], $indexed[2]);
    assertSame([1, 2], array_keys($indexed));
});

exit(summary());

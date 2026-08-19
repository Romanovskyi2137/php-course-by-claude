<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: swap()');
test('міняє місцями два елементи', fn () => assertSame([20, 10], swap([10, 20])));
test('працює з рядками', fn () => assertSame(['b', 'a'], swap(['a', 'b'])));

section('Вимога 2: merge_configs()');
test('overrides перезаписують спільні ключі', function (): void {
    assertSame(
        ['timeout' => 30, 'retries' => 5],
        merge_configs(['timeout' => 30, 'retries' => 3], ['retries' => 5])
    );
});
test('ключі лише в overrides додаються', function (): void {
    assertSame(['a' => 1, 'b' => 2], merge_configs(['a' => 1], ['b' => 2]));
});

section('Вимога 3: append()');
test('додає елемент в кінець', fn () => assertSame([1, 2, 3], append([1, 2], 3)));
test('не змінює оригінальний масив', function (): void {
    $original = [1, 2];
    append($original, 3);
    assertSame([1, 2], $original);
});

section('Вимога 4: pluck()');
test('витягує значення за ключем з кожного запису', function (): void {
    $records = [
        ['id' => 1, 'name' => 'Ada'],
        ['id' => 2, 'name' => 'Alan'],
    ];
    assertSame(['Ada', 'Alan'], pluck($records, 'name'));
    assertSame([1, 2], pluck($records, 'id'));
});
test('порожній список -> порожній масив', fn () => assertSame([], pluck([], 'name')));

exit(summary());

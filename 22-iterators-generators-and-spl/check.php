<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: count_up()');
test('повертає саме Generator', function (): void {
    assertTrue(count_up(1, 1) instanceof \Generator);
});
test('видає числа від $from до $to включно', function (): void {
    assertSame([1, 2, 3, 4, 5], iterator_to_array(count_up(1, 5), false));
});
test('однакове $from і $to видає один елемент', function (): void {
    assertSame([7], iterator_to_array(count_up(7, 7), false));
});

section('Вимога 2: flatten()');
test('повертає саме Generator', function (): void {
    assertTrue(flatten([1]) instanceof \Generator);
});
test('розгортає довільно вкладені масиви в плоску послідовність', function (): void {
    assertSame([1, 2, 3, 4, 5], iterator_to_array(flatten([1, [2, 3, [4]], 5]), false));
});
test('порожній масив дає порожню послідовність', function (): void {
    assertSame([], iterator_to_array(flatten([]), false));
});

section('Вимога 3: Playlist');
test('foreach перебирає треки в порядку зі збереженням ключів 0..n-1', function (): void {
    $playlist = new Playlist(['Track A', 'Track B', 'Track C']);
    $collected = [];
    foreach ($playlist as $key => $track) {
        $collected[$key] = $track;
    }
    assertSame(['Track A', 'Track B', 'Track C'], $collected);
});
test('count() повертає кількість треків', function (): void {
    assertSame(3, (new Playlist(['A', 'B', 'C']))->count());
});
test('повторний foreach працює завдяки rewind()', function (): void {
    $playlist = new Playlist(['A', 'B']);
    $first = [];
    foreach ($playlist as $track) {
        $first[] = $track;
    }
    $second = [];
    foreach ($playlist as $track) {
        $second[] = $track;
    }
    assertSame($first, $second);
});

section('Вимога 4: RecentlyPlayed');
test('вміщує до capacity елементів у порядку додавання', function (): void {
    $history = new RecentlyPlayed(3);
    $history->push('t1');
    $history->push('t2');
    assertSame(['t1', 't2'], $history->toArray());
});
test('перевищення capacity витісняє найстаріший елемент', function (): void {
    $history = new RecentlyPlayed(3);
    foreach (['t1', 't2', 't3', 't4', 't5'] as $track) {
        $history->push($track);
    }
    assertSame(['t3', 't4', 't5'], $history->toArray());
});

exit(summary());

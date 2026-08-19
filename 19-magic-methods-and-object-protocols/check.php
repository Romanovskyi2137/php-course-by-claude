<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: DynamicBag');
test('__set/__get працюють для довільної назви', function (): void {
    $bag = new DynamicBag();
    $bag->color = 'red';
    assertSame('red', $bag->color);
});
test('невстановлена властивість повертає null', function (): void {
    $bag = new DynamicBag();
    assertNull($bag->missing);
});
test('__isset() відображає реальний стан', function (): void {
    $bag = new DynamicBag();
    assertSame(false, isset($bag->color));
    $bag->color = 'blue';
    assertSame(true, isset($bag->color));
});

section('Вимога 2: Money implements Stringable');
test('__toString() форматує центи як гроші', function (): void {
    assertSame('12.50', (string) new Money(1250));
});
test('нуль форматується як 0.00', fn () => assertSame('0.00', (string) new Money(0)));
test('автоматично instanceof Stringable', function (): void {
    assertTrue(new Money(100) instanceof \Stringable);
});

section('Вимога 3: Config implements ArrayAccess, Countable');
test('читання й запис через []', function (): void {
    $config = new Config(['debug' => true]);
    assertSame(true, $config['debug']);
    $config['env'] = 'prod';
    assertSame('prod', $config['env']);
});
test('isset()/unset() працюють через []', function (): void {
    $config = new Config(['debug' => true]);
    assertSame(true, isset($config['debug']));
    unset($config['debug']);
    assertSame(false, isset($config['debug']));
});
test('count() повертає кількість ключів', function (): void {
    $config = new Config(['a' => 1, 'b' => 2]);
    assertSame(2, count($config));
});

exit(summary());

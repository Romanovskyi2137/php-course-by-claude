<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertPropertyIsReadonly, assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Vector2D через promotion, readonly');
test('конструктор встановлює x і y', function (): void {
    $v = new Vector2D(1.0, 2.0);
    assertSame(1.0, $v->x);
    assertSame(2.0, $v->y);
});
test('x і y дійсно readonly', function (): void {
    assertPropertyIsReadonly(Vector2D::class, 'x');
    assertPropertyIsReadonly(Vector2D::class, 'y');
});

section('Вимога 2: Vector2D::add()');
test('повертає новий вектор із сумою координат', function (): void {
    $a = new Vector2D(1.0, 2.0);
    $b = new Vector2D(3.0, 4.0);
    $sum = $a->add($b);
    assertSame(4.0, $sum->x);
    assertSame(6.0, $sum->y);
});
test('не змінює операнди', function (): void {
    $a = new Vector2D(1.0, 2.0);
    $b = new Vector2D(3.0, 4.0);
    $a->add($b);
    assertSame(1.0, $a->x);
    assertSame(3.0, $b->x);
});

section('Вимога 3: readonly забороняє повторний запис');
test('запис у readonly-властивість кидає Error', function (): void {
    $v = new Vector2D(1.0, 2.0);
    assertThrows(\Error::class, function () use ($v): void {
        $v->x = 99.0;
    });
});

section('Вимога 4: Tag, Box, __clone()');
test('Box зберігає Tag', function (): void {
    $box = new Box(new Tag('fragile'));
    assertSame('fragile', $box->tag->name);
});
test('клон має незалежний Tag (глибоке клонування)', function (): void {
    $original = new Box(new Tag('fragile'));
    $copy = clone $original;
    $copy->tag->name = 'changed';

    assertSame('fragile', $original->tag->name);
    assertSame('changed', $copy->tag->name);
});

exit(summary());

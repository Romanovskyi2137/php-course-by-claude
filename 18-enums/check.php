<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, assertCount, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Status backed enum');
test('три кейси з очікуваними значеннями', function (): void {
    assertSame('draft', Status::Draft->value);
    assertSame('published', Status::Published->value);
    assertSame('archived', Status::Archived->value);
});
test('Status::cases() повертає всі три кейси', function (): void {
    assertCount(3, Status::cases());
});
test('Status::from() знаходить кейс за значенням', function (): void {
    assertSame(Status::Draft, Status::from('draft'));
});

section('Вимога 2: Status::label()');
test('label() для кожного кейса', function (): void {
    assertSame('Чернетка', Status::Draft->label());
    assertSame('Опубліковано', Status::Published->label());
    assertSame('Архів', Status::Archived->label());
});

section('Вимога 3: next_status()');
test('Draft -> Published', fn () => assertSame(Status::Published, next_status(Status::Draft)));
test('Published -> Archived', fn () => assertSame(Status::Archived, next_status(Status::Published)));
test('Archived -> Archived (кінцевий стан)', fn () => assertSame(Status::Archived, next_status(Status::Archived)));

section('Вимога 4: parse_status()');
test("'published' -> Status::Published", fn () => assertSame(Status::Published, parse_status('published')));
test("невідоме значення -> null, без винятку", fn () => assertNull(parse_status('unknown')));

exit(summary());

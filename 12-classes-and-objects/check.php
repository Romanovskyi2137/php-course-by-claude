<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Point3D конструктор і геттери');
test('геттери повертають передані координати', function (): void {
    $p = new Point3D(1.0, 2.0, 3.0);
    assertSame(1.0, $p->x());
    assertSame(2.0, $p->y());
    assertSame(3.0, $p->z());
});

section('Вимога 2: Point3D::length()');
test('обчислює довжину радіус-вектора', function (): void {
    $p = new Point3D(3.0, 4.0, 0.0);
    assertSame(5.0, $p->length());
});
test('точка в нулі має нульову довжину', function (): void {
    $p = new Point3D(0.0, 0.0, 0.0);
    assertSame(0.0, $p->length());
});

section('Вимога 3: Point3D::withX() мутує обʼєкт');
test('withX() змінює x для того ж обʼєкта', function (): void {
    $p = new Point3D(1.0, 2.0, 3.0);
    $p->withX(10.0);
    assertSame(10.0, $p->x());
});
test('два хендли на один обʼєкт бачать зміну (reference semantics)', function (): void {
    $a = new Point3D(1.0, 2.0, 3.0);
    $b = $a;
    $b->withX(99.0);
    assertSame(99.0, $a->x());
});

section('Вимога 4: Address, Profile, profile_city()');
test('повертає місто, якщо адреса є', function (): void {
    $profile = new Profile();
    $profile->address = new Address('Львів');
    assertSame('Львів', profile_city($profile));
});
test('повертає null, якщо адреси немає', function (): void {
    $profile = new Profile();
    assertNull(profile_city($profile));
});

exit(summary());

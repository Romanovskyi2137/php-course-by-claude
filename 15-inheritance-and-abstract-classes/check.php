<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Shape — абстрактний клас і Template Method');
test('Shape не можна інстанціювати напряму', function (): void {
    assertThrows(\Error::class, fn () => new Shape());
});

section('Вимога 2: Circle');
test('area() = pi * r^2', function (): void {
    $circle = new Circle(2.0);
    assertSame(round(M_PI * 4, 5), round($circle->area(), 5));
});
test('describe() використовує area() через Template Method', function (): void {
    $circle = new Circle(1.0);
    assertSame(sprintf('Площа: %.2f', M_PI), $circle->describe());
});

section('Вимога 3: Square');
test('area() = side^2', function (): void {
    $square = new Square(3.0);
    assertSame(9.0, $square->area());
});
test('describe() форматує площу квадрата', function (): void {
    $square = new Square(4.0);
    assertSame('Площа: 16.00', $square->describe());
});

section('Вимога 4: Animal/Dog — parent::__construct() і parent::describe()');
test('Animal::describe() базовий формат', function (): void {
    $animal = new Animal('Рекс');
    assertSame("Тварина на ім'я Рекс", $animal->describe());
});
test('Dog::describe() розширює Animal::describe()', function (): void {
    $dog = new Dog('Рекс', 'лабрадор');
    assertSame("Тварина на ім'я Рекс, порода лабрадор", $dog->describe());
});

exit(summary());

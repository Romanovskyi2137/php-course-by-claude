<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, requireComposerAutoload, section, summary, test};

$target = getenv('CHECK_TARGET') ?: 'starter';

if (!requireComposerAutoload(__DIR__ . "/{$target}/vendor/autoload.php")) {
    exit(summary());
}

section('Вимога 1-2: App\\Money');
test('конструктор через promotion, властивості доступні', function (): void {
    $money = new \App\Money(1500, 'UAH');
    assertSame(1500, $money->cents);
    assertSame('UAH', $money->currency);
});
test('add() тієї самої валюти повертає нову Money із сумою', function (): void {
    $sum = (new \App\Money(1000, 'UAH'))->add(new \App\Money(500, 'UAH'));
    assertSame(1500, $sum->cents);
    assertSame('UAH', $sum->currency);
});
test('add() різних валют кидає InvalidArgumentException', function (): void {
    assertThrows(\InvalidArgumentException::class, function (): void {
        (new \App\Money(1000, 'UAH'))->add(new \App\Money(500, 'USD'));
    });
});
test('format() форматує як "15.00 UAH"', function (): void {
    assertSame('15.00 UAH', (new \App\Money(1500, 'UAH'))->format());
});

section('Вимога 3: App\\Cart');
test('total() сумує всі додані товари', function (): void {
    $cart = new \App\Cart();
    $cart->add(new \App\Money(1000, 'UAH'));
    $cart->add(new \App\Money(250, 'UAH'));
    $cart->add(new \App\Money(50, 'UAH'));
    assertSame('13.00 UAH', $cart->total()->format());
});
test('total() для порожнього кошика кидає RuntimeException', function (): void {
    assertThrows(\RuntimeException::class, fn () => (new \App\Cart())->total());
});

exit(summary());

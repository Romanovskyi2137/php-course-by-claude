<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, assertThrows, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: InsufficientFundsException');
test('є нащадком RuntimeException', function (): void {
    assertTrue(new InsufficientFundsException() instanceof \RuntimeException);
});

section('Вимога 2: Account::withdraw()');
test('успішне зняття зменшує баланс', function (): void {
    $account = new Account(100.0);
    $account->withdraw(30.0);
    assertSame(70.0, $account->balance());
});
test('зняття понад баланс кидає InsufficientFundsException', function (): void {
    $account = new Account(50.0);
    assertThrows(InsufficientFundsException::class, function () use ($account): void {
        $account->withdraw(100.0);
    });
});
test('невдале зняття не змінює баланс', function (): void {
    $account = new Account(50.0);
    try {
        $account->withdraw(100.0);
    } catch (InsufficientFundsException) {
        // очікувано
    }
    assertSame(50.0, $account->balance());
});

section('Вимога 3: safe_withdraw()');
test('true при успішному знятті', function (): void {
    assertSame(true, safe_withdraw(new Account(100.0), 30.0));
});
test('false при нестачі коштів, без пропущеного винятку', function (): void {
    assertSame(false, safe_withdraw(new Account(10.0), 100.0));
});

section('Вимога 4: safe_divide()');
test('звичайне ділення повертає float', fn () => assertSame(2.5, safe_divide(5, 2)));
test('ділення на нуль повертає null, не падає', fn () => assertNull(safe_divide(5, 0)));

exit(summary());

<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1-2: Book implements Priced');
test('інтерфейс Priced оголошує метод price()', function (): void {
    assertTrue(method_exists(Priced::class, 'price'));
});
test('Book — інстанс Priced', function (): void {
    assertTrue(new Book(10.0) instanceof Priced);
});
test('Book::price() повертає вартість', function (): void {
    assertSame(15.5, (new Book(15.5))->price());
});

section('Вимога 3: Ticket implements Priced');
test('Ticket::price() = basePrice + fee', function (): void {
    assertSame(22.5, (new Ticket(20.0, 2.5))->price());
});

section('Вимога 4: total_price()');
test('сумує price() різних типів через поліморфізм', function (): void {
    $items = [new Book(10.0), new Ticket(20.0, 2.5)];
    assertSame(32.5, total_price($items));
});
test('порожній масив -> 0.0', fn () => assertSame(0.0, total_price([])));
test('кидає InvalidArgumentException для елемента без Priced', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => total_price([new Book(1.0), 'не Priced']));
});

section('Вимога 5: most_expensive()');
test('повертає обʼєкт з найбільшою ціною', function (): void {
    $book = new Book(10.0);
    $ticket = new Ticket(50.0, 5.0);
    assertSame($ticket, most_expensive([$book, $ticket]));
});
test('повертає сам обʼєкт, а не число', function (): void {
    $book = new Book(99.0);
    assertTrue(most_expensive([$book]) instanceof Priced);
});

exit(summary());

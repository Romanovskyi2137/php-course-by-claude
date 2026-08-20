<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertNull, assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Strategy');
test('NoDiscount не змінює ціну', function (): void {
    $calc = new PriceCalculator(new NoDiscount());
    assertEquals(100.0, $calc->finalPrice(100.0));
});
test('PercentageDiscount зменшує ціну на відсоток', function (): void {
    $calc = new PriceCalculator(new PercentageDiscount(10));
    assertEquals(90.0, $calc->finalPrice(100.0));
});

section('Вимога 2: Observer');
test('notify() сповіщає всіх підписників у порядку підписки', function (): void {
    $emitter = new EventEmitter();
    $a = new LoggingObserver();
    $b = new LoggingObserver();
    $emitter->subscribe($a);
    $emitter->subscribe($b);
    $emitter->notify('order.placed');
    $emitter->notify('order.shipped');
    assertSame(['order.placed', 'order.shipped'], $a->log());
    assertSame(['order.placed', 'order.shipped'], $b->log());
});
test('без підписників notify() нічого не ламає', function (): void {
    (new EventEmitter())->notify('no-op');
});

section('Вимога 3: Repository');
test('save()/findByEmail() зберігає й повертає користувача', function (): void {
    $repo = new InMemoryUserRepository();
    $repo->save(['email' => 'user@example.com', 'name' => 'Іван']);
    assertSame(['email' => 'user@example.com', 'name' => 'Іван'], $repo->findByEmail('user@example.com'));
});
test('findByEmail() для незбереженого email повертає null', function (): void {
    assertNull((new InMemoryUserRepository())->findByEmail('nobody@example.com'));
});

section('Вимога 4: Service layer (RegistrationService)');
test('register() зберігає нового користувача й повертає true', function (): void {
    $repo = new InMemoryUserRepository();
    $service = new RegistrationService($repo);
    $result = $service->register('user@example.com', 'Іван');
    assertSame(true, $result);
    assertSame(['email' => 'user@example.com', 'name' => 'Іван'], $repo->findByEmail('user@example.com'));
});
test('register() з уже зайнятим email повертає false і не перезаписує', function (): void {
    $repo = new InMemoryUserRepository();
    $service = new RegistrationService($repo);
    $service->register('user@example.com', 'Іван');
    $result = $service->register('user@example.com', 'Хтось інший');
    assertSame(false, $result);
    assertSame('Іван', $repo->findByEmail('user@example.com')['name']);
});
test('RegistrationService працює з будь-якою реалізацією UserRepository (тут — InMemory)', function (): void {
    $repo = new InMemoryUserRepository();
    $service = new RegistrationService($repo);
    assertSame(true, $service->register('a@example.com', 'A'));
    assertSame(true, $service->register('b@example.com', 'B'));
});

exit(summary());

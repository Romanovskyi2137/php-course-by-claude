<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Logger / ArrayLogger');
test('log() зберігає повідомлення, messages() повертає всі', function (): void {
    $logger = new ArrayLogger();
    $logger->log('перше');
    $logger->log('друге');
    assertSame(['перше', 'друге'], $logger->messages());
});
test('ArrayLogger реалізує Logger', function (): void {
    assertTrue(new ArrayLogger() instanceof Logger);
});

section('Вимога 2: OrderService (DI через конструктор)');
test('placeOrder() логує повідомлення через впроваджений Logger', function (): void {
    $logger = new ArrayLogger();
    $service = new OrderService($logger);
    $service->placeOrder('Дриль');
    assertSame(['Order placed: Дриль'], $logger->messages());
});
test('різні екземпляри Logger дають незалежні логи', function (): void {
    $loggerA = new ArrayLogger();
    $loggerB = new ArrayLogger();
    (new OrderService($loggerA))->placeOrder('A');
    (new OrderService($loggerB))->placeOrder('B');
    assertSame(['Order placed: A'], $loggerA->messages());
    assertSame(['Order placed: B'], $loggerB->messages());
});

section('Вимога 3: Container');
test('set()/get() повертає результат фабрики', function (): void {
    $container = new Container();
    $container->set('logger', fn () => new ArrayLogger());
    assertTrue($container->get('logger') instanceof ArrayLogger);
});
test('get() повертає той самий екземпляр повторно (singleton-поведінка)', function (): void {
    $container = new Container();
    $container->set('logger', fn () => new ArrayLogger());
    $first = $container->get('logger');
    $second = $container->get('logger');
    assertTrue($first === $second);
});
test('фабрика не викликається, поки сервіс жодного разу не запитали', function (): void {
    $calls = 0;
    $container = new Container();
    $container->set('logger', function () use (&$calls) {
        $calls++;
        return new ArrayLogger();
    });
    assertSame(0, $calls);
    $container->get('logger');
    assertSame(1, $calls);
    $container->get('logger');
    assertSame(1, $calls);
});
test('has() true для зареєстрованого, false для незареєстрованого', function (): void {
    $container = new Container();
    $container->set('logger', fn () => new ArrayLogger());
    assertSame(true, $container->has('logger'));
    assertSame(false, $container->has('unknown'));
});
test('get() для незареєстрованого id кидає OutOfBoundsException', function (): void {
    assertThrows(\OutOfBoundsException::class, fn () => (new Container())->get('unknown'));
});
test('фабрика отримує сам контейнер як аргумент (може дістати інші залежності)', function (): void {
    $container = new Container();
    $container->set(Logger::class, fn () => new ArrayLogger());
    $container->set(OrderService::class, fn (Container $c) => new OrderService($c->get(Logger::class)));

    $service = $container->get(OrderService::class);
    assertTrue($service instanceof OrderService);
});

exit(summary());

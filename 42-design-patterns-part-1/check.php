<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertEquals, assertSame, assertThrows, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: Factory');
test('ShapeFactory створює Circle з правильною площею', function (): void {
    $circle = ShapeFactory::create('circle', 2.0);
    assertTrue($circle instanceof Circle);
    assertEquals(M_PI * 4, $circle->area());
});
test('ShapeFactory створює Rectangle з правильною площею', function (): void {
    $rect = ShapeFactory::create('rectangle', 3.0, 4.0);
    assertTrue($rect instanceof Rectangle);
    assertEquals(12.0, $rect->area());
});
test('невідомий тип кидає InvalidArgumentException', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => ShapeFactory::create('triangle', 1.0));
});

section('Вимога 2: Builder');
test('build() без умов', function (): void {
    $sql = (new QueryBuilder())->select('id', 'name')->from('products')->build();
    assertSame('SELECT id, name FROM products', $sql);
});
test('build() з кількома умовами через AND', function (): void {
    $sql = (new QueryBuilder())
        ->select('id')
        ->from('products')
        ->where('price > 10')
        ->where('in_stock = 1')
        ->build();
    assertSame('SELECT id FROM products WHERE price > 10 AND in_stock = 1', $sql);
});

section('Вимога 3: Singleton');
test('getInstance() завжди повертає той самий екземпляр', function (): void {
    assertTrue(AppConfig::getInstance() === AppConfig::getInstance());
});
test('set()/get() зберігають значення між викликами getInstance()', function (): void {
    AppConfig::getInstance()->set('env', 'production');
    assertSame('production', AppConfig::getInstance()->get('env'));
});
test('get() для незаданого ключа повертає null', function (): void {
    assertSame(null, AppConfig::getInstance()->get('nonexistent-key'));
});

section('Вимога 4: Decorator');
test('SimpleCoffee — базові cost/description', function (): void {
    $coffee = new SimpleCoffee();
    assertSame(30.0, $coffee->cost());
    assertSame('Кава', $coffee->description());
});
test('MilkDecorator додає ціну й опис поверх обгорнутого Coffee', function (): void {
    $coffee = new MilkDecorator(new SimpleCoffee());
    assertSame(40.0, $coffee->cost());
    assertSame('Кава + молоко', $coffee->description());
});
test('декоратори комбінуються (Sugar поверх Milk поверх Simple)', function (): void {
    $coffee = new SugarDecorator(new MilkDecorator(new SimpleCoffee()));
    assertSame(45.0, $coffee->cost());
    assertSame('Кава + молоко + цукор', $coffee->description());
});

exit(summary());

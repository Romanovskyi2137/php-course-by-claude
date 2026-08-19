<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertTrue, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: IdGenerator');
test('перший next() повертає 1, наступні зростають', function (): void {
    IdGenerator::reset();
    assertSame(1, IdGenerator::next());
    assertSame(2, IdGenerator::next());
    assertSame(3, IdGenerator::next());
});
test('current() повертає останнє видане число без інкременту', function (): void {
    IdGenerator::reset();
    IdGenerator::next();
    IdGenerator::next();
    assertSame(2, IdGenerator::current());
    assertSame(2, IdGenerator::current());
});
test('reset() скидає до 0', function (): void {
    IdGenerator::next();
    IdGenerator::reset();
    assertSame(0, IdGenerator::current());
});

section('Вимога 2: self:: vs static:: у Model/Product');
test('Model::tableViaSelf() і tableViaStatic() обидва models', function (): void {
    assertSame('models', Model::tableViaSelf());
    assertSame('models', Model::tableViaStatic());
});
test('Product::tableViaSelf() лишається models (self прив\'язаний до Model)', function (): void {
    assertSame('models', Product::tableViaSelf());
});
test('Product::tableViaStatic() -> products (LSB бачить реальний клас виклику)', function (): void {
    assertSame('products', Product::tableViaStatic());
});

section('Вимога 3: Model::create() з new static()');
test('Model::create() повертає екземпляр Model', function (): void {
    assertTrue(Model::create() instanceof Model);
});
test('Widget::create() повертає екземпляр Widget, не Model', function (): void {
    $widget = Widget::create();
    assertTrue($widget instanceof Widget);
});

exit(summary());

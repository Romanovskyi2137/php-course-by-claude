<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertFalse, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: register_autoloader()');
test('до реєстрації клас App\\Domain\\Product ще не завантажений', function (): void {
    assertFalse(class_exists('App\\Domain\\Product', false));
});
test('register_autoloader() не кидає помилок', function (): void {
    register_autoloader(__DIR__ . '/classes');
});

section('Вимога 2: make_product() підвантажує клас автоматично');
test('повертає коректно ініціалізований Product', function (): void {
    $product = make_product('Книга', 10.0);
    assertSame('Книга', $product->name);
    assertSame(10.0, $product->price);
});
test('клас реально підвантажився через автозавантажувач', function (): void {
    assertSame(true, class_exists('App\\Domain\\Product', false));
});

section('Вимога 3: shout_via_autoload()');
test('викликає App\\Support\\StringHelper::shout()', function (): void {
    assertSame('ПРИВІТ', shout_via_autoload('привіт'));
});

exit(summary());

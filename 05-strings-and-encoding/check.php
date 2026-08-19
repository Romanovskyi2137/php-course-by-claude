<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: greet()');
test('вітає латиницею', fn () => assertSame('Привіт, Ada!', greet('Ada')));
test('вітає кирилицею', fn () => assertSame('Привіт, Ада!', greet('Ада')));

section('Вимога 2: char_count()');
test('латиниця: байти == символи', fn () => assertSame(5, char_count('hello')));
test('кирилиця: символи, НЕ байти', fn () => assertSame(6, char_count('Привіт')));

section('Вимога 3: shout()');
test('латиниця у верхній регістр', fn () => assertSame('HELLO', shout('hello')));
test('кирилиця у верхній регістр', fn () => assertSame('ПРИВІТ', shout('привіт')));

section('Вимога 4: truncate()');
test('короткий текст не змінюється', fn () => assertSame('Привіт', truncate('Привіт', 10)));
test('текст рівно maxLength не змінюється', fn () => assertSame('Привіт', truncate('Привіт', 6)));
test('довгий кириличний текст обрізається по СИМВОЛАХ і додає …', function (): void {
    assertSame('При…', truncate('Привіт', 3));
});
test('довгий латинський текст обрізається і додає …', function (): void {
    assertSame('Hel…', truncate('Hello world', 3));
});

exit(summary());

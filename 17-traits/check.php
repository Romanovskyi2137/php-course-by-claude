<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertNull, assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1-3: Loggable + Timestamped в Article');
test('log()/logs() накопичують повідомлення по порядку', function (): void {
    $article = new Article();
    $article->log('created');
    $article->log('published');
    assertSame(['created', 'published'], $article->logs());
});
test('createdAt() null до touch()', function (): void {
    $article = new Article();
    assertNull($article->createdAt());
});
test('touch() записує сьогоднішню дату у форматі Y-m-d', function (): void {
    $article = new Article();
    $article->touch();
    assertSame(date('Y-m-d'), $article->createdAt());
});
test('різні екземпляри Article мають незалежні логи', function (): void {
    $a = new Article();
    $b = new Article();
    $a->log('only in a');
    assertSame(['only in a'], $a->logs());
    assertSame([], $b->logs());
});

section('Вимога 4: Robot — вирішення конфлікту трейтів');
test('hello() використовує версію з Greets', function (): void {
    assertSame('Hello from Greets', (new Robot())->hello());
});
test('speaksHello() дає доступ до версії з Speaks', function (): void {
    assertSame('Hello from Speaks', (new Robot())->speaksHello());
});

exit(summary());

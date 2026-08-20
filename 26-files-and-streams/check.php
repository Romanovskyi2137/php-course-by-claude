<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

function withTempFile(string $contents, callable $fn): void
{
    $path = tempnam(sys_get_temp_dir(), 'course26');
    file_put_contents($path, $contents);
    try {
        $fn($path);
    } finally {
        @unlink($path);
    }
}

section('Вимога 1: read_lines() та append_log()');
test('read_lines() повертає рядки без \\n', function (): void {
    withTempFile("line one\nline two\nline three\n", function (string $path): void {
        assertSame(['line one', 'line two', 'line three'], read_lines($path));
    });
});
test('append_log() дописує в кінець, не стираючи наявний вміст', function (): void {
    withTempFile("existing line\n", function (string $path): void {
        append_log($path, 'new line');
        assertSame(['existing line', 'new line'], read_lines($path));
    });
});

section('Вимога 3: read_csv_as_assoc()');
test('перший рядок стає заголовками, решта — асоціативними рядками', function (): void {
    withTempFile("id,name\n1,Anna\n2,Ivan\n", function (string $path): void {
        assertSame(
            [
                ['id' => '1', 'name' => 'Anna'],
                ['id' => '2', 'name' => 'Ivan'],
            ],
            read_csv_as_assoc($path)
        );
    });
});

section('Вимога 4: count_words_in_stream()');
test('рахує сумарну кількість слів у файлі', function (): void {
    withTempFile("Hello world\nThis is PHP\n", function (string $path): void {
        assertSame(5, count_words_in_stream($path));
    });
});
test('порожній файл дає 0 слів', function (): void {
    withTempFile('', function (string $path): void {
        assertSame(0, count_words_in_stream($path));
    });
});

exit(summary());

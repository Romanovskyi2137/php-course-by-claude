<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: validate_registration()');
test('валідні дані -> valid true, порожні errors', function (): void {
    $result = validate_registration(['email' => 'user@example.com', 'password' => 'longenough', 'age' => '25']);
    assertSame(true, $result['valid']);
    assertSame([], $result['errors']);
});
test('некоректний email додає помилку', function (): void {
    $result = validate_registration(['email' => 'not-an-email', 'password' => 'longenough', 'age' => '25']);
    assertSame(false, $result['valid']);
    assertSame(true, array_key_exists('email', $result['errors']));
});
test('короткий пароль додає помилку', function (): void {
    $result = validate_registration(['email' => 'user@example.com', 'password' => 'short', 'age' => '25']);
    assertSame(true, array_key_exists('password', $result['errors']));
});
test('вік поза діапазоном додає помилку', function (): void {
    $result = validate_registration(['email' => 'user@example.com', 'password' => 'longenough', 'age' => '5']);
    assertSame(true, array_key_exists('age', $result['errors']));
});

section('Вимога 2: redirect_url()');
test('без query повертає лише шлях', fn () => assertSame('/success', redirect_url('/success')));
test('з query додає ?', function (): void {
    assertSame('/order?status=ok&id=42', redirect_url('/order', ['status' => 'ok', 'id' => 42]));
});

section('Вимога 3: is_allowed_upload()');
test('дозволяє файл дозволеного реального типу', function (): void {
    $tmp = tempnam(sys_get_temp_dir(), 'upload');
    file_put_contents($tmp, 'just plain text content');
    $file = ['error' => UPLOAD_ERR_OK, 'tmp_name' => $tmp, 'type' => 'image/png'];
    assertSame(true, is_allowed_upload($file, ['text/plain']));
    unlink($tmp);
});
test('відхиляє файл недозволеного реального типу, попри заявлений type', function (): void {
    $tmp = tempnam(sys_get_temp_dir(), 'upload');
    file_put_contents($tmp, 'just plain text content');
    $file = ['error' => UPLOAD_ERR_OK, 'tmp_name' => $tmp, 'type' => 'text/plain'];
    assertSame(false, is_allowed_upload($file, ['image/png', 'image/jpeg']));
    unlink($tmp);
});
test('відхиляє файл з помилкою завантаження', function (): void {
    $file = ['error' => UPLOAD_ERR_INI_SIZE, 'tmp_name' => '', 'type' => 'text/plain'];
    assertSame(false, is_allowed_upload($file, ['text/plain']));
});

section('Вимога 4: human_readable_size()');
test('байти лишаються як є', fn () => assertSame('500 B', human_readable_size(500)));
test('кілобайти', fn () => assertSame('1.5 KB', human_readable_size(1536)));
test('мегабайти', fn () => assertSame('5 MB', human_readable_size(5 * 1024 * 1024)));

exit(summary());

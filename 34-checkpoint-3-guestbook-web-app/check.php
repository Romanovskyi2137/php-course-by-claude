<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '/src/classes.php';

section('Вимога 1: GuestbookEntry');
test('властивості доступні через promotion', function (): void {
    $entry = new GuestbookEntry(1, 'Іван', 'Привіт!', '2026-08-20 10:00:00');
    assertSame(1, $entry->id);
    assertSame('Іван', $entry->name);
    assertSame('Привіт!', $entry->message);
    assertSame('2026-08-20 10:00:00', $entry->createdAt);
});
test('toArray() повертає скалярні значення', function (): void {
    $entry = new GuestbookEntry(1, 'Іван', 'Привіт!', '2026-08-20 10:00:00');
    assertSame([
        'id' => 1,
        'name' => 'Іван',
        'message' => 'Привіт!',
        'createdAt' => '2026-08-20 10:00:00',
    ], $entry->toArray());
});

section('Вимога 2: GuestbookRepository');
function makeEntry(int $id, string $name = 'X'): GuestbookEntry
{
    return new GuestbookEntry($id, $name, 'msg', '2026-08-20 10:00:00');
}

test('add()/all() — найновіші першими', function (): void {
    $repo = new GuestbookRepository();
    $repo->add(makeEntry(1));
    $repo->add(makeEntry(2));
    $repo->add(makeEntry(3));
    assertSame([3, 2, 1], array_map(fn (GuestbookEntry $e) => $e->id, $repo->all()));
});
test('конструктор приймає початковий список записів', function (): void {
    $repo = new GuestbookRepository([makeEntry(1), makeEntry(2)]);
    assertSame([2, 1], array_map(fn (GuestbookEntry $e) => $e->id, $repo->all()));
});
test('deleteById() видаляє запис за id', function (): void {
    $repo = new GuestbookRepository([makeEntry(1), makeEntry(2), makeEntry(3)]);
    $repo->deleteById(2);
    assertSame([3, 1], array_map(fn (GuestbookEntry $e) => $e->id, $repo->all()));
});
test('deleteById() для неіснуючого id нічого не ламає', function (): void {
    $repo = new GuestbookRepository([makeEntry(1)]);
    $repo->deleteById(999);
    assertSame([1], array_map(fn (GuestbookEntry $e) => $e->id, $repo->all()));
});
test('nextId() — 1 для порожнього репозиторію', function (): void {
    assertSame(1, (new GuestbookRepository())->nextId());
});
test('nextId() — max(id) + 1', function (): void {
    $repo = new GuestbookRepository([makeEntry(1), makeEntry(5), makeEntry(3)]);
    assertSame(6, $repo->nextId());
});
test('all() повторний виклик не змінює порядок додавання всередині репозиторію', function (): void {
    $repo = new GuestbookRepository();
    $repo->add(makeEntry(1));
    $repo->add(makeEntry(2));
    $repo->all();
    $repo->add(makeEntry(3));
    assertSame([3, 2, 1], array_map(fn (GuestbookEntry $e) => $e->id, $repo->all()));
});

section('Вимога 2: fromJsonFile() / toJsonFile()');
test('toJsonFile() записує, fromJsonFile() читає той самий стан', function (): void {
    $repo = new GuestbookRepository([makeEntry(1, 'Ольга'), makeEntry(2, 'Петро')]);
    $path = tempnam(sys_get_temp_dir(), 'guestbook') . '.json';
    try {
        $repo->toJsonFile($path);
        $loaded = GuestbookRepository::fromJsonFile($path);
        assertSame([2, 1], array_map(fn (GuestbookEntry $e) => $e->id, $loaded->all()));
        assertSame('Ольга', array_values(array_filter($loaded->all(), fn ($e) => $e->id === 1))[0]->name);
    } finally {
        @unlink($path);
    }
});

section('Вимога 3: validate_entry()');
test('коректні дані -> valid true', function (): void {
    $result = validate_entry(['name' => 'Іван', 'message' => 'Привіт!']);
    assertSame(true, $result['valid']);
    assertSame([], $result['errors']);
});
test('порожнє ім\'я додає помилку', function (): void {
    $result = validate_entry(['name' => '  ', 'message' => 'Привіт!']);
    assertSame(true, array_key_exists('name', $result['errors']));
});
test('задовге повідомлення додає помилку', function (): void {
    $result = validate_entry(['name' => 'Іван', 'message' => str_repeat('a', 501)]);
    assertSame(true, array_key_exists('message', $result['errors']));
});

section('Вимога 4: CSRF-функції');
test('generate_csrf_token() повертає 64 hex-символи', function (): void {
    assertSame(1, preg_match('/^[0-9a-f]{64}$/', generate_csrf_token()));
});
test('verify_csrf_token() true для однакових, false для різних чи null', function (): void {
    assertSame(true, verify_csrf_token('abc', 'abc'));
    assertSame(false, verify_csrf_token('abc', 'xyz'));
    assertSame(false, verify_csrf_token(null, 'abc'));
    assertSame(false, verify_csrf_token('abc', null));
});

section('Вимога 5: attempt_admin_login()');
test('true для вірного пароля', function (): void {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    assertSame(true, attempt_admin_login('admin123', $hash));
});
test('false для невірного пароля', function (): void {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    assertSame(false, attempt_admin_login('wrong', $hash));
});

exit(summary());

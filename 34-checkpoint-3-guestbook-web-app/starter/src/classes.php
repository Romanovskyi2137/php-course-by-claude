<?php

declare(strict_types=1);

// Checkpoint 3: гостьова книга. Опис вимог — у task.md.

// TODO (вимога 1): readonly DTO запису гостьової книги.
final class GuestbookEntry
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toArray(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 2): сховище записів у пам'яті з JSON-персистенцією.
final class GuestbookRepository
{
    public function __construct(array $entries = [])
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function add(GuestbookEntry $entry): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function all(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function deleteById(int $id): void
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function nextId(): int
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public static function fromJsonFile(string $path): self
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toJsonFile(string $path): void
    {
        throw new \RuntimeException('Не реалізовано');
    }
}

// TODO (вимога 3): валідація нового запису.
function validate_entry(array $input): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): CSRF-токен — генерація і null-безпечна перевірка за постійний час.
function generate_csrf_token(): string
{
    throw new \RuntimeException('Не реалізовано');
}

function verify_csrf_token(?string $expected, ?string $actual): bool
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 5): перевірка пароля адміна.
function attempt_admin_login(string $password, string $storedHash): bool
{
    throw new \RuntimeException('Не реалізовано');
}

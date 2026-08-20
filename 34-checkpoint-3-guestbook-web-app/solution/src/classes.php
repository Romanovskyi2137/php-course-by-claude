<?php

declare(strict_types=1);

// Checkpoint 3: гостьова книга — референсний розв'язок.

final class GuestbookEntry
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $message,
        public readonly string $createdAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'message' => $this->message,
            'createdAt' => $this->createdAt,
        ];
    }
}

final class GuestbookRepository
{
    public function __construct(private array $entries = [])
    {
    }

    public function add(GuestbookEntry $entry): void
    {
        $this->entries[] = $entry;
    }

    public function all(): array
    {
        $entries = $this->entries;
        usort($entries, static fn (GuestbookEntry $a, GuestbookEntry $b): int => $b->id <=> $a->id);

        return $entries;
    }

    public function deleteById(int $id): void
    {
        $this->entries = array_values(array_filter(
            $this->entries,
            static fn (GuestbookEntry $entry): bool => $entry->id !== $id
        ));
    }

    public function nextId(): int
    {
        if ($this->entries === []) {
            return 1;
        }

        return max(array_map(static fn (GuestbookEntry $entry): int => $entry->id, $this->entries)) + 1;
    }

    public static function fromJsonFile(string $path): self
    {
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException("Не вдалось прочитати {$path}");
        }

        $rows = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);
        $entries = array_map(
            static fn (array $row): GuestbookEntry => new GuestbookEntry(
                id: $row['id'],
                name: $row['name'],
                message: $row['message'],
                createdAt: $row['createdAt'],
            ),
            $rows
        );

        return new self($entries);
    }

    public function toJsonFile(string $path): void
    {
        $rows = array_map(static fn (GuestbookEntry $entry): array => $entry->toArray(), $this->entries);
        file_put_contents(
            $path,
            json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}

function validate_entry(array $input): array
{
    $errors = [];
    $name = trim($input['name'] ?? '');
    $message = trim($input['message'] ?? '');

    if ($name === '' || mb_strlen($name) > 50) {
        $errors['name'] = "Ім'я має бути від 1 до 50 символів";
    }

    if ($message === '' || mb_strlen($message) > 500) {
        $errors['message'] = 'Повідомлення має бути від 1 до 500 символів';
    }

    return ['valid' => $errors === [], 'errors' => $errors];
}

function generate_csrf_token(): string
{
    return bin2hex(random_bytes(32));
}

function verify_csrf_token(?string $expected, ?string $actual): bool
{
    if ($expected === null || $actual === null) {
        return false;
    }

    return hash_equals($expected, $actual);
}

function attempt_admin_login(string $password, string $storedHash): bool
{
    return password_verify($password, $storedHash);
}

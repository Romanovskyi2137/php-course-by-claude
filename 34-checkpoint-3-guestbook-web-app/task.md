# Checkpoint 3: вимоги

## Мета

Зібрати в одному проєкті: валідацію форми (тема 30), CSRF-захист і
екранування виводу (тема 33), сесії (тема 31), автентифікацію через
`password_hash`/`password_verify` (тема 32), JSON-персистенцію (теми 25-26)
і `readonly`-DTO (тема 13).

## Що робимо

Реалізуємо клас `GuestbookEntry`, клас `GuestbookRepository` і чотири
функції у `starter/src/classes.php`. Вся CLI-обгортка веб-застосунку
(`starter/public/*.php`) вже написана за тебе — подивись туди, щоб
зрозуміти, як класи й функції використовуються разом у реальних
`index.php`/`submit.php`/`login.php`/`logout.php`/`delete.php`.

## Вимоги

1. `readonly`-клас `GuestbookEntry` через constructor promotion:
   `int $id`, `string $name`, `string $message`, `string $createdAt`;
   метод `toArray(): array` — `['id' => ..., 'name' => ..., 'message' =>
   ..., 'createdAt' => ...]`.

2. Клас `GuestbookRepository`:
   - `__construct(array $entries = [])`;
   - `add(GuestbookEntry $entry): void`;
   - `all(): array` — усі записи, **найновіші першими**;
   - `deleteById(int $id): void` — видаляє запис за `id` (якщо його
     немає — нічого не робить, не кидає виняток);
   - `nextId(): int` — `1`, якщо записів немає, інакше `max(id) + 1`;
   - `static fromJsonFile(string $path): self`;
   - `toJsonFile(string $path): void` (через `toArray()` кожного запису,
     `JSON_PRETTY_PRINT`).

3. `validate_entry(array $input)` — валідує `['name' => ..., 'message'
   => ...]`: `name` — непорожній рядок (після `trim()`) не довший за 50
   символів; `message` — непорожній рядок не довший за 500 символів.
   Повертає `['valid' => bool, 'errors' => array]` (як
   `validate_registration()` у темі 30).

4. CSRF-функції (як у темі 33): `generate_csrf_token(): string` і
   `verify_csrf_token(?string $expected, ?string $actual): bool`.

5. `attempt_admin_login(string $password, string $storedHash)` — `true`,
   якщо `$password` відповідає `$storedHash` (`password_verify()`).

## Контракт (сигнатури)

```php
final class GuestbookEntry
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $message,
        public readonly string $createdAt,
    )
    public function toArray(): array
}

final class GuestbookRepository
{
    public function __construct(array $entries = [])
    public function add(GuestbookEntry $entry): void
    public function all(): array
    public function deleteById(int $id): void
    public function nextId(): int
    public static function fromJsonFile(string $path): self
    public function toJsonFile(string $path): void
}

function validate_entry(array $input): array
function generate_csrf_token(): string
function verify_csrf_token(?string $expected, ?string $actual): bool
function attempt_admin_login(string $password, string $storedHash): bool
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `GuestbookEntry` — саме `readonly`-властивості через promotion.
- `GuestbookRepository::all()` не мутує внутрішній стан репозиторію при
  сортуванні (використай `usort()` над копією чи `array_reverse()`
  після сортування за id — деталі на твій вибір, головне — оригінальний
  порядок додавання лишається незмінним для наступних викликів `all()`).
- `verify_csrf_token()` порівнює через `hash_equals()`, не `===`.

## Як перевірити

```bash
php check.php
```

Після зеленого `check.php` — запусти реальний застосунок:

```bash
php -S localhost:8000 -t starter/public
```

Відкрий `http://localhost:8000/`:
1. Залиш запис через форму — з'явиться у списку, найновіший згори.
2. Спробуй відправити форму з порожнім іменем — побачиш помилку
   валідації, введене повідомлення не втратиться.
3. Залогинься як адмін (пароль `admin123`) — біля кожного запису
   з'явиться посилання "Видалити".
4. Видали запис, вийди з адмінки — посилання "Видалити" зникнуть.

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] реальний застосунок у браузері: форма працює з валідацією,
      адмін може залогинитись і видалити запис, гість — ні
- [ ] можеш пояснити, чому `delete.php` перевіряє одразу і сесію
      (`$_SESSION['admin']`), і CSRF-токен, а не щось одне з двох

## Підказки

<details>
<summary>Підказка 1 — GuestbookEntry</summary>

```php
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
```
</details>

<details>
<summary>Підказка 2 — GuestbookRepository::all() і nextId()</summary>

```php
public function all(): array
{
    $entries = $this->entries;
    usort($entries, static fn (GuestbookEntry $a, GuestbookEntry $b) => $b->id <=> $a->id);

    return $entries;
}

public function nextId(): int
{
    if ($this->entries === []) {
        return 1;
    }

    return max(array_map(static fn (GuestbookEntry $e) => $e->id, $this->entries)) + 1;
}
```
</details>

<details>
<summary>Підказка 3 — validate_entry()</summary>

```php
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
```
</details>

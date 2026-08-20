# Завдання 46: автентифікація API, помилки та логування

## Підготовка

```bash
cd 46-api-auth-errors-and-logging/starter && composer install && cd ..
```

## Мета

1. Дістати й перевірити Bearer-токен із заголовків запиту.
2. Побудувати уніфіковану відповідь-помилку API.
3. Реалізувати PSR-3-подібний логер зі структурованим контекстом.

Тести вже написані в `tests/` — твоя робота лише в `src/`.

## Вимоги

1. `extract_bearer_token(array $server): ?string` — дістає токен із
   `$server['HTTP_AUTHORIZATION']` формату `'Bearer <token>'`; `null`,
   якщо заголовка немає чи він не в цьому форматі.
2. `authenticate(array $server, array $validTokens): ?string` —
   `$validTokens` — асоціативний масив `[токен => userId]`. Повертає
   `userId` для дійсного токена, `null` — якщо токена немає чи він
   недійсний.
3. `error_response(string $message, int $status): array` — повертає
   `['status' => $status, 'headers' => ['Content-Type' =>
   'application/json'], 'body' => json_encode(['error' => $message],
   JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]` (`JSON_UNESCAPED_UNICODE`
   — щоб кириличні повідомлення лишались читабельними, тема 25).
4. Інтерфейс `Logger` (`info(string $message, array $context = []):
   void`, `error(string $message, array $context = []): void`); клас
   `ArrayLogger implements Logger` — зберігає кожен запис як
   `['level' => 'info'|'error', 'message' => ..., 'context' => ...]`;
   `entries(): array` повертає всі записи в порядку додавання.

## Контракт (сигнатури)

```php
namespace App;

function extract_bearer_token(array $server): ?string
function authenticate(array $server, array $validTokens): ?string
function error_response(string $message, int $status): array

interface Logger
{
    public function info(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}

final class ArrayLogger implements Logger
{
    public function entries(): array
}
```

## Обмеження

- Кожен файл починається з `declare(strict_types=1);`, одразу після —
  `namespace App;`.
- `authenticate()` не звертається до `$_SERVER` напряму — лише через
  параметр.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] `php check.php` (PHPUnit) зелений
- [ ] можеш пояснити, чому JWT (розділ 3.2 `lecture.md`) не варто
      використовувати для зберігання пароля користувача в payload

## Бонус (не перевіряється автоматично)

Додай метод `Logger::warning(string $message, array $context = []):
void` і реалізуй його в `ArrayLogger`.

<details>
<summary>Підказка 1</summary>

```php
function extract_bearer_token(array $server): ?string
{
    $header = $server['HTTP_AUTHORIZATION'] ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        return null;
    }

    return substr($header, strlen('Bearer '));
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function authenticate(array $server, array $validTokens): ?string
{
    $token = extract_bearer_token($server);

    return $token !== null ? ($validTokens[$token] ?? null) : null;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
final class ArrayLogger implements Logger
{
    private array $entries = [];

    public function info(string $message, array $context = []): void
    {
        $this->entries[] = ['level' => 'info', 'message' => $message, 'context' => $context];
    }

    public function error(string $message, array $context = []): void
    {
        $this->entries[] = ['level' => 'error', 'message' => $message, 'context' => $context];
    }

    public function entries(): array
    {
        return $this->entries;
    }
}
```
</details>

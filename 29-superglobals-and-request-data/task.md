# Завдання 29: суперглобали та дані запиту

## Мета

1. Дістати IP клієнта з масиву `$_SERVER`-подібних даних, з урахуванням проксі.
2. Розпізнати JSON-запит за `Content-Type`.
3. Розпарсити "сире" JSON-тіло (`php://input`) — застосування теми 25.
4. Відфільтрувати вхідні параметри за білим списком дозволених ключів.

Усі функції приймають дані запиту як **параметри** (масиви), а не читають
`$_GET`/`$_POST`/`$_SERVER` напряму — так їх можна перевірити без
реального HTTP-запиту (розділ 4 `lecture.md`).

## Вимоги

1. `client_ip(array $server)` — повертає IP клієнта: якщо є
   `$server['HTTP_X_FORWARDED_FOR']` — перший IP зі списку (через кому),
   обрізаний від пробілів; інакше `$server['REMOTE_ADDR']`; якщо немає
   жодного — `'unknown'`.
2. `is_json_request(array $server)` — `true`, якщо
   `$server['CONTENT_TYPE']` містить `'application/json'` (можливо, з
   додатковим `; charset=...` після).
3. `parse_json_body(string $rawBody)` — декодує JSON-тіло в асоціативний
   масив; кидає `\JsonException` при некоректному JSON.
4. `sanitize_get_params(array $get, array $allowedKeys)` — повертає
   новий масив лише з ключів `$get`, що є в `$allowedKeys`, зі
   значеннями, обрізаними від пробілів (`trim()`); ключі поза білим
   списком — відкидаються.

## Контракт (сигнатури)

```php
function client_ip(array $server): string
function is_json_request(array $server): bool
function parse_json_body(string $rawBody): array
function sanitize_get_params(array $get, array $allowedKeys): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Жодна з функцій не звертається до `$_GET`/`$_POST`/`$_SERVER` напряму
  — лише через параметри.
- `sanitize_get_params()` не додає ключів, яких немає у вхідному `$get`,
  навіть якщо вони є в `$allowedKeys`.

## Як перевірити

```bash
php check.php
```

Далі — реальний сервер, поза автоматичною перевіркою:

```bash
php -S localhost:8000 -t public
```

Відкрий `http://localhost:8000/?q=php&page=2&secret=ignored` — побачиш,
як `sanitize_get_params()` відфільтровує `secret` разом з реальними
суперглобалами `$_GET`/`$_SERVER`.

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] `public/index.php` реально показує відфільтровані GET-параметри й
      IP клієнта з реального запиту в браузері
- [ ] можеш пояснити, чому жодна з чотирьох функцій не читає `$_GET`/
      `$_SERVER` сама

## Бонус (не перевіряється автоматично)

Додай `request_content_length(array $server): ?int` — повертає
`$server['CONTENT_LENGTH']` як `int`, або `null`, якщо заголовка немає
чи він не є числом.

<details>
<summary>Підказка 1</summary>

```php
function client_ip(array $server): string
{
    if (isset($server['HTTP_X_FORWARDED_FOR'])) {
        [$first] = explode(',', $server['HTTP_X_FORWARDED_FOR']);
        return trim($first);
    }

    return $server['REMOTE_ADDR'] ?? 'unknown';
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function is_json_request(array $server): bool
{
    return str_contains($server['CONTENT_TYPE'] ?? '', 'application/json');
}

function parse_json_body(string $rawBody): array
{
    return json_decode($rawBody, associative: true, flags: JSON_THROW_ON_ERROR);
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function sanitize_get_params(array $get, array $allowedKeys): array
{
    $result = [];
    foreach ($allowedKeys as $key) {
        if (array_key_exists($key, $get)) {
            $result[$key] = trim((string) $get[$key]);
        }
    }

    return $result;
}
```
</details>

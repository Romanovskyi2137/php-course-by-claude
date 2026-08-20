# Завдання 33: основи веб-безпеки

## Мета

1. Екранувати вивід проти XSS.
2. Згенерувати й перевірити CSRF-токен за постійний час.
3. Відфільтрувати вхідні дані за білим списком проти mass assignment.
4. Побудувати значення заголовка CSP.

## Вимоги

1. `escape_html(string $value)` — екранує рядок для безпечного виводу в
   HTML через `htmlspecialchars()` з `ENT_QUOTES | ENT_SUBSTITUTE`, UTF-8.
2. `generate_csrf_token()` — повертає новий випадковий токен: 64
   hex-символи (`bin2hex(random_bytes(32))`).
3. `verify_csrf_token(?string $expected, ?string $actual)` — `true`
   лише якщо обидва аргументи не `null` і рівні за постійний час
   (`hash_equals()`); `false` в усіх інших випадках (включно з тим, коли
   якийсь із аргументів `null`).
4. `filter_allowed_fields(array $input, array $allowedFields)` —
   повертає новий масив лише з ключів `$input`, що є в `$allowedFields`
   (mass assignment guard).
5. `content_security_policy_header(array $directives)` — будує значення
   заголовка `Content-Security-Policy` з асоціативного масиву
   `['директива' => 'значення']`, розділяючи директиви `; `.

## Контракт (сигнатури)

```php
function escape_html(string $value): string
function generate_csrf_token(): string
function verify_csrf_token(?string $expected, ?string $actual): bool
function filter_allowed_fields(array $input, array $allowedFields): array
function content_security_policy_header(array $directives): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `verify_csrf_token()` порівнює саме через `hash_equals()`, не `===`.
- `escape_html()` екранує й одинарні, і подвійні лапки (`ENT_QUOTES`).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому `verify_csrf_token()` повертає `false`, а не
      кидає виняток, коли токена немає в сесії

## Бонус (не перевіряється автоматично)

Додай `strip_dangerous_protocols(string $url): ?string` — повертає
`$url`, якщо вона починається з `http://`/`https://`/`/` (відносний
шлях), інакше `null` (захист від `javascript:`-посилань, підкладених
через поле "мій сайт" у формі).

<details>
<summary>Підказка 1</summary>

```php
function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_csrf_token(): string
{
    return bin2hex(random_bytes(32));
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function verify_csrf_token(?string $expected, ?string $actual): bool
{
    if ($expected === null || $actual === null) {
        return false;
    }

    return hash_equals($expected, $actual);
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function content_security_policy_header(array $directives): string
{
    $parts = [];
    foreach ($directives as $name => $value) {
        $parts[] = "{$name} {$value}";
    }

    return implode('; ', $parts);
}
```
</details>

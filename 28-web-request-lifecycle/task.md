# Завдання 28: життєвий цикл веб-запиту

## Мета

1. Розібрати рядок HTTP-запиту й код відповіді на структуровані дані.
2. Побудувати `query string` для URL.
3. Побачити реальний `php -S` і `header()` у дії — у `public/`, поза
   автоматичною перевіркою.

## Вимоги

1. `parse_request_line(string $requestLine)` — розбирає рядок виду
   `"GET /users?id=5 HTTP/1.1"` у масив `['method' => 'GET', 'path' =>
   '/users', 'query' => 'id=5', 'protocolVersion' => '1.1']`. Якщо `?` у
   цілі запиту немає — `'query'` дорівнює `null`.
2. `reason_phrase(int $statusCode)` — повертає стандартну фразу-пояснення
   для коду статусу (`200` → `'OK'`, `404` → `'Not Found'` тощо, повний
   список — у контракті нижче); кидає `\InvalidArgumentException` для
   коду, якого немає в списку.
3. `status_class(int $statusCode)` — повертає клас коду:
   `'informational'` (1xx), `'success'` (2xx), `'redirect'` (3xx),
   `'client_error'` (4xx), `'server_error'` (5xx).
4. `build_query_string(array $params)` — будує query-рядок з
   асоціативного масиву (без початкового `?`).

## Контракт (сигнатури)

```php
function parse_request_line(string $requestLine): array
function reason_phrase(int $statusCode): string
function status_class(int $statusCode): string
function build_query_string(array $params): string
```

Повний список кодів для `reason_phrase()`: `200` OK, `201` Created,
`204` No Content, `301` Moved Permanently, `302` Found,
`304` Not Modified, `400` Bad Request, `401` Unauthorized,
`403` Forbidden, `404` Not Found, `405` Method Not Allowed,
`422` Unprocessable Entity, `500` Internal Server Error,
`503` Service Unavailable.

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `build_query_string()` коректно кодує спецсимволи й пробіли (`http_build_query()`).
- `status_class()` для коду поза діапазоном 100-599 кидає
  `\InvalidArgumentException`.

## Як перевірити

```bash
php check.php
```

Далі — реальний сервер, поза автоматичною перевіркою:

```bash
php -S localhost:8000 -t public
```

Відкрий `http://localhost:8000/` у браузері — побачиш інформацію про
поточний запит, зібрану саме функціями з `starter.php`.

## Definition of Done

- [ ] усі перевірки check.php зелені
- [ ] `php -S localhost:8000 -t public` реально піднімається, і сторінка
      в браузері показує метод, шлях і query поточного запиту
- [ ] можеш пояснити, чому `public/index.php` викликає `parse_request_line()`
      над `$_SERVER['REQUEST_METHOD']`/`REQUEST_URI`, а не отримує ці дані
      якось інакше

## Бонус (не перевіряється автоматично)

Спробуй додати другу сторінку `public/about.php` і відкрити її в
браузері поруч з `public/index.php` — зверни увагу, що для цього не
знадобився жоден "роутер": вбудований сервер сам мапить URL на файл. Це
й показує, чого саме позбавляє тебе front controller (тема 45).

<details>
<summary>Підказка 1</summary>

```php
function parse_request_line(string $requestLine): array
{
    [$method, $target, $protocol] = explode(' ', $requestLine);
    [$path, $query] = str_contains($target, '?')
        ? explode('?', $target, 2)
        : [$target, null];

    return [
        'method' => $method,
        'path' => $path,
        'query' => $query,
        'protocolVersion' => str_replace('HTTP/', '', $protocol),
    ];
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
function status_class(int $statusCode): string
{
    return match (true) {
        $statusCode >= 100 && $statusCode < 200 => 'informational',
        $statusCode >= 200 && $statusCode < 300 => 'success',
        $statusCode >= 300 && $statusCode < 400 => 'redirect',
        $statusCode >= 400 && $statusCode < 500 => 'client_error',
        $statusCode >= 500 && $statusCode < 600 => 'server_error',
        default => throw new \InvalidArgumentException("Некоректний код статусу: {$statusCode}"),
    };
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function build_query_string(array $params): string
{
    return http_build_query($params);
}
```
</details>

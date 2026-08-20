# Завдання 45: REST API на голому PHP

## Підготовка

```bash
cd 45-rest-api-with-plain-php/starter && composer install && cd ..
```

## Мета

1. Побудувати мінімальний роутер: реєстрація маршрутів, параметри
   шляху, `404`/`405`.
2. Реалізувати узгодження вмісту (`Accept`-заголовок).
3. Побудувати JSON-відповідь як дані, не побічний ефект.

Тести вже написані в `tests/` — твоя робота лише в `src/RestApi.php`.

## Вимоги

1. `Router::register(string $method, string $pattern, callable
   $handler): void` — реєструє маршрут; `$pattern` може містити
   параметри у фігурних дужках (`/users/{id}`).
2. `Router::dispatch(string $method, string $path): array` — знаходить
   перший зареєстрований маршрут, чий патерн і метод збігаються з
   переданими; викликає `$handler($params)` (де `$params` — асоціативний
   масив іменованих параметрів шляху) і повертає його результат. Якщо
   жоден маршрут не збігається за шляхом — `['status' => 404, 'body' =>
   ['error' => 'Not Found']]`. Якщо шлях збігається, але не для цього
   методу — `['status' => 405, 'body' => ['error' => 'Method Not
   Allowed']]`.
3. `negotiate_content_type(string $acceptHeader, array $available):
   string` — перший тип з `$acceptHeader` (розділені комою, можливий
   `;q=...` після типу — ігноруй), що є в `$available`; `'*/*'` означає
   "будь-що" — повертає перший з `$available`. Якщо нічого не
   збіглось — теж перший з `$available`.
4. `json_response(mixed $data, int $status = 200): array` — повертає
   `['status' => $status, 'headers' => ['Content-Type' =>
   'application/json'], 'body' => json_encode($data,
   JSON_THROW_ON_ERROR)]`.

## Контракт (сигнатури)

```php
namespace App;

final class Router
{
    public function register(string $method, string $pattern, callable $handler): void
    public function dispatch(string $method, string $path): array
}

function negotiate_content_type(string $acceptHeader, array $available): string
function json_response(mixed $data, int $status = 200): array
```

## Обмеження

- Файл `src/RestApi.php` починається з `declare(strict_types=1);`,
  одразу після — `namespace App;`.
- `Router::dispatch()` перевіряє маршрути в порядку реєстрації —
  перший, що збігається і за шляхом, і за методом, виграє.

## Як перевірити

```bash
php check.php
```

Далі — реальний сервер, поза автоматичною перевіркою:

```bash
php -S localhost:8000 -t public
```

Відкрий `http://localhost:8000/users/42` і `http://localhost:8000/nope`
— побач роутер у дії з реальними HTTP-запитами.

## Definition of Done

- [ ] `php check.php` (PHPUnit) зелений
- [ ] `public/index.php` реально маршрутизує запити в браузері
- [ ] можеш пояснити, чому `/users/new` треба зареєструвати **до**
      `/users/{id}`, а не після

## Бонус (не перевіряється автоматично)

Додай підтримку кількох параметрів в одному патерні
(`/users/{userId}/orders/{orderId}`) — переконайся, що `$params`
міститиме обидва ключі.

<details>
<summary>Підказка 1</summary>

```php
public function register(string $method, string $pattern, callable $handler): void
{
    $regex = preg_replace('#\{(\w+)\}#', '(?<$1>[^/]+)', $pattern);
    $this->routes[] = [
        'method' => strtoupper($method),
        'regex' => '#^' . $regex . '$#',
        'handler' => $handler,
    ];
}
```
</details>

<details>
<summary>Підказка 2</summary>

```php
public function dispatch(string $method, string $path): array
{
    $pathMatchedAnyMethod = false;

    foreach ($this->routes as $route) {
        if (preg_match($route['regex'], $path, $matches) !== 1) {
            continue;
        }
        if ($route['method'] !== strtoupper($method)) {
            $pathMatchedAnyMethod = true;
            continue;
        }
        $params = array_filter($matches, fn ($key) => is_string($key), ARRAY_FILTER_USE_KEY);

        return ($route['handler'])($params);
    }

    return $pathMatchedAnyMethod
        ? ['status' => 405, 'body' => ['error' => 'Method Not Allowed']]
        : ['status' => 404, 'body' => ['error' => 'Not Found']];
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function negotiate_content_type(string $acceptHeader, array $available): string
{
    foreach (array_map('trim', explode(',', $acceptHeader)) as $entry) {
        $type = explode(';', $entry)[0];

        if ($type === '*/*') {
            return $available[0];
        }
        if (in_array($type, $available, true)) {
            return $type;
        }
    }

    return $available[0];
}
```
</details>

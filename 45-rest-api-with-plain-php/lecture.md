# 45. REST API на голому PHP

> Що ти вже вмієш: Express-роутер/Flask route decorators з іншої мови —
> та сама ідея · Що додає ця тема: побудувати мінімальний роутер
> власноруч, щоб побачити, що саме роблять фреймворки "під капотом" ·
> ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Роутер, що мапить `(метод, шлях)` на обробник — той самий принцип,
  що `app.get('/users/:id', handler)` в Express чи `@app.route('/users/<id>')`
  у Flask. PHP не має вбудованого роутера (на відміну від Express,
  вбудованого в саму бібліотеку) — кожен фреймворк (Laravel, Symfony,
  Slim) має власний, побудований на тій самій ідеї, що й тут.
- Content negotiation — той самий механізм HTTP, що використовує будь-
  який REST API незалежно від мови бекенду: заголовок `Accept` каже
  клієнту, що він хоче отримати, і сервер обирає найкращий доступний
  формат.
- REST — архітектурний стиль, не бібліотека й не протокол — ті самі
  принципи (ресурси, HTTP-методи як дієслова, статус-коди як
  результат) незалежно від мови реалізації.

## 2. Ключові терміни

- **Route (маршрут)** — пара "патерн шляху + HTTP-метод", зв'язана з
  обробником.
- **Path parameter (параметр шляху)** — змінна частина шляху
  (`/users/{id}` — `id` підставляється з реального URL, `/users/42` →
  `id = '42'`).
- **Content negotiation (узгодження вмісту)** — вибір формату відповіді
  на основі заголовка `Accept` запиту клієнта.
- **405 Method Not Allowed** — статус, коли шлях існує, але не для
  цього HTTP-методу (на відміну від `404` — шляху взагалі немає).

## 3. Теорія

### 3.1 Мінімальний роутер: патерн → обробник

```php
final class Router
{
    private array $routes = [];

    public function register(string $method, string $pattern, callable $handler): void
    {
        $regex = preg_replace('#\{(\w+)\}#', '(?<$1>[^/]+)', $pattern);
        $this->routes[] = [
            'method' => strtoupper($method),
            'regex' => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

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
}

$router = new Router();
$router->register('GET', '/users/{id}', fn (array $params) => [
    'status' => 200,
    'body' => ['id' => $params['id']],
]);

$router->dispatch('GET', '/users/42'); // ['status' => 200, 'body' => ['id' => '42']]
```

`{id}` у патерні перетворюється на іменовану групу захоплення (тема 24)
— той самий механізм regex, застосований до нової задачі.

### 3.2 Content negotiation: заголовок `Accept`

```php
function negotiate_content_type(string $acceptHeader, array $available): string
{
    foreach (array_map('trim', explode(',', $acceptHeader)) as $entry) {
        $type = explode(';', $entry)[0]; // відкидаємо q=0.9 та подібне

        if ($type === '*/*' || in_array($type, $available, true)) {
            return $type === '*/*' ? $available[0] : $type;
        }
    }

    return $available[0]; // типовий формат, якщо нічого не підійшло
}

negotiate_content_type('application/json, text/html;q=0.9', ['application/json', 'text/html']);
// 'application/json'
```

### 3.3 Побудова JSON-відповіді як дані, не побічний ефект

```php
function json_response(mixed $data, int $status = 200): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode($data, JSON_THROW_ON_ERROR),
    ];
}
```

Той самий принцип, що в темі 28: функція повертає **дані**, що
описують відповідь, а не викликає `header()`/`echo` напряму — тому
`json_response()` (і весь `Router`) можна протестувати (тема 44) без
реального HTTP-запиту. Front controller (`public/index.php`) —
єдине місце, де ці дані нарешті перетворюються в реальні
`header()`/`echo`.

## 4. Підводні камені

- Роутер, що перевіряє маршрути **лінійно** зверху вниз (як у розділі
  3.1), — цілком нормально для невеликого API; для тисяч маршрутів
  промислові роутери (Symfony, FastRoute) використовують дерева
  префіксів для швидшого пошуку — не хвилюйся про це для навчального
  проєкту.
- Забутий порядок реєстрації: якщо `/users/new` зареєстровано
  **після** `/users/{id}`, другий патерн "зжере" `/users/new` першим
  (бо `{id}` збігається з будь-яким сегментом, включно з `'new'`) —
  конкретні маршрути реєструють **до** параметризованих.
- `404` і `405` — це не одне й те саме: `404` каже "такого ресурсу
  немає взагалі", `405` — "ресурс є, але не цей метод" (клієнт APIs
  іноді покладаються на цю різницю, щоб зрозуміти, чи варто пробувати
  інший метод).

## 5. Сучасний PHP 8.3+

Написання роутера власноруч — навчальна вправа, не рекомендація для
продакшену: реальні проєкти беруть готовий, добре протестований
роутер (`nikic/fast-route`, роутер Symfony чи Laravel) замість
переписування цієї логіки заново. Розуміння, як він працює зсередини —
мета цієї теми, а не заміна готових бібліотек.

## 6. Контекст екосистеми

Кожен PHP REST API-фреймворк (Slim, Laravel, Symfony) — це, по суті,
front controller (тема 28) + роутер (розділ 3.1) + купа зручностей
навколо (middleware, валідація, серіалізація). Checkpoint 5 (тема 49)
збере власний маленький API на основі саме цих ідей.

## 7. Документація

- https://www.php.net/manual/en/function.preg-match.php
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Content_negotiation
- https://github.com/nikic/FastRoute

## 8. Питання для самоперевірки

1. Як `{id}` у патерні маршруту перетворюється на реальне значення з
   URL?
2. Чому `/users/new` треба реєструвати до `/users/{id}`, а не після?
3. У чому різниця між `404` і `405` з точки зору того, що саме
   відбулось?
4. Чому `json_response()` повертає масив, а не викликає `header()`/
   `echo` напряму?

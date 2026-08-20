# 46. Автентифікація API, помилки та логування

> Що ти вже вмієш: Bearer-токени/JWT з іншої мови чи курсу — та сама
> ідея скрізь · Що додає ця тема: PSR-3 — стандартизований інтерфейс
> логера, спільний для всієї PHP-екосистеми · ~30 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Bearer-токен (`Authorization: Bearer <token>`) — стандартний
  HTTP-заголовок, той самий у будь-якій мові бекенду: не PHP-специфіка,
  а частина HTTP-специфікації (RFC 6750).
- JWT (JSON Web Token) — теж мовонезалежний формат: підписаний
  base64-JSON, який будь-яка мова може перевірити тим самим алгоритмом
  (HMAC чи RSA). У цій темі — лише концептуальний огляд, без реалізації
  підпису вручну (для продакшену беруть готову бібліотеку, не пишуть
  крипто-код самі).
- PSR-3 (`LoggerInterface`) — те, чим для логування є `winston`/`pino`
  (Node) чи стандартний `logging`-модуль Python: єдиний контракт
  (`info()`, `error()`, `warning()` тощо), яким користуються різні
  бібліотеки логування, взаємозамінно.

## 2. Ключові терміни

- **Bearer-токен** — рядок-секрет, що передається в заголовку
  `Authorization: Bearer <token>`; сервер перевіряє його й ідентифікує
  клієнта — без сесій/куків (тема 31), типово для API.
- **JWT (JSON Web Token)** — самодостатній підписаний токен: `header.payload.signature`
  (усі три частини — base64), payload містить дані (наприклад,
  `user_id`), підпис гарантує, що payload не підмінили.
- **PSR-3 (Logger Interface)** — стандарт PHP-FIG: інтерфейс
  `LoggerInterface` з методами за рівнями важливості (`emergency`,
  `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`).
- **Structured logging (структуроване логування)** — запис логів у
  машинозчитуваному форматі (типово JSON) з контекстними полями, а не
  довільний текстовий рядок — простіше парсити й фільтрувати пізніше.

## 3. Теорія

### 3.1 Bearer-токен: перевірка запиту

```php
function extract_bearer_token(array $server): ?string
{
    $header = $server['HTTP_AUTHORIZATION'] ?? '';

    if (!str_starts_with($header, 'Bearer ')) {
        return null;
    }

    return substr($header, strlen('Bearer '));
}

function authenticate(array $server, array $validTokens): ?string
{
    $token = extract_bearer_token($server);

    return $token !== null ? ($validTokens[$token] ?? null) : null; // повертає userId чи null
}
```

### 3.2 JWT: структура (концептуально)

```
eyJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo0Mn0.4X9k...
└── header (base64) ─┘└── payload (base64) ─┘└─ підпис ─┘

header:  {"alg": "HS256"}      — який алгоритм підпису
payload: {"user_id": 42}       — довільні дані ("claims")
signature: HMAC-SHA256(header + '.' + payload, секретний ключ сервера)
```

Сервер перевіряє токен, перерахувавши підпис із власного секретного
ключа й порівнявши з тим, що прийшов — якщо збігається, payload не
підмінений (і, як наслідок, йому можна довіряти). Реалізація підпису/
перевірки вручну — поза межами цієї теми: у продакшн-коді для цього
беруть бібліотеку (`firebase/php-jwt` чи подібну), а не пишуть HMAC
самостійно.

### 3.3 Уніфіковані помилки API

```php
function error_response(string $message, int $status): array
{
    return [
        'status' => $status,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
    ];
}

// у роутері (тема 45):
if ($token === null) {
    return error_response('Не авторизовано', 401);
}
```

Консистентна структура помилки (`{"error": "..."}`) для **всіх**
провалів API — клієнту не доводиться вгадувати формат відповіді залежно
від того, яка саме помилка сталась.

### 3.4 PSR-3: інтерфейс логера

```php
interface Logger
{
    public function info(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}

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

    public function entries(): array { return $this->entries; }
}

$logger->error('Не вдалось обробити замовлення', ['orderId' => 42, 'reason' => 'timeout']);
```

Другий параметр `$context` — масив довільних додаткових даних; разом з
рядком повідомлення це і є "структуроване логування" (розділ 2) — легко
знайти всі логи для `orderId = 42` пізніше, фільтруючи за полем
контексту, а не парсячи текст повідомлення регуляркою.

## 4. Підводні камені

- Bearer-токен, переданий у **query string** (`?token=...`) замість
  заголовка — потрапляє в логи веб-сервера, історію браузера, `Referer`
  сусідніх запитів. Завжди `Authorization`-заголовок, ніколи URL.
- JWT **не шифрує** payload — лише підписує. Будь-хто може
  base64-декодувати JWT і прочитати вміст (спробуй на будь-якому
  jwt.io-подібному сервісі) — не клади туди паролі чи інші секрети,
  лише несекретні ідентифікаційні дані (`user_id`, `role`).
- Логування **самого токена/пароля** в контексті помилки
  (`$logger->error('Login failed', ['password' => $password])`) —
  реальна вразливість: логи часто менш захищені й довше зберігаються,
  ніж основна БД. Ніколи не клади секрети в лог, навіть для дебагу.

## 5. Сучасний PHP 8.3+

`psr/log` (пакет з інтерфейсами PSR-3) — окрема, дуже маленька
Composer-залежність (лише інтерфейси, без реалізації) — практично
кожна бібліотека логування (Monolog — фактичний стандарт екосистеми)
її реалізує, тому код, написаний проти `Psr\Log\LoggerInterface`,
працює з будь-яким PSR-3-сумісним логером без змін.

## 6. Контекст екосистеми

Monolog — де-факто стандартна бібліотека логування для PHP,
реалізує PSR-3 і вміє писати одночасно в кілька "handlers" (файл,
stderr, зовнішній сервіс типу Sentry) з різними рівнями фільтрації.
Checkpoint 5 (тема 49) використає структуру Bearer-автентифікації й
уніфікованих помилок із цієї теми в реальному REST API.

## 7. Документація

- https://www.php-fig.org/psr/psr-3/
- https://jwt.io/introduction
- https://github.com/Seldaek/monolog

## 8. Питання для самоперевірки

1. Чим Bearer-токен відрізняється від сесійної куки (тема 31) з точки
   зору того, де зберігається стан?
2. Чому JWT називають "підписаним", а не "зашифрованим", і чому це
   важливо для того, що можна класти в payload?
3. Навіщо потрібен уніфікований формат помилки API (`{"error": "..."}`),
   якщо можна просто повертати текст помилки як є?
4. Чому не можна логувати пароль користувача навіть у контексті
   повідомлення про помилку логіну?

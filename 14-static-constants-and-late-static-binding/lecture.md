# 14. Статичні члени, константи класу та пізнє статичне зв'язування

> Що ти вже вмієш: `static` поля/методи з іншої мови · Що додає ця тема:
> найспецифічнішу PHP-концепцію в ООП — різницю `self` і `static` · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Статичні властивості/методи концептуально ті самі, що в Java/C# — належать
  класу, не екземпляру. Синтаксис звернення `ClassName::$prop` /
  `ClassName::method()` схожий на `ClassName.staticMethod()` в Java.
- **Late static binding (`static::`) — це те, чого немає прямого еквіваленту
  в JS/Python.** Найближчий аналог — `cls` у Python `@classmethod`, який
  теж "знає", з якого підкласу його викликали. Java/C# такого механізму
  не мають взагалі (статичні методи не поліморфні).
- Константи класу (`const` всередині `class`) схожі на `static readonly`
  поля C# чи `static final` Java.

## 2. Ключові терміни

- **Late static binding (пізнє статичне зв'язування)** — `static::` всередині
  методу посилається на клас, **з якого метод був фактично викликаний**
  (навіть через успадкований метод), на відміну від `self::`, яке завжди
  посилається на клас, **де метод оголошено**.
- **Named constructor (іменований конструктор)** — статичний фабричний метод,
  що повертає новий екземпляр класу, часто з промовистою назвою
  (`User::fromArray(...)` замість перевантаженого `__construct`).

## 3. Теорія

### 3.1 Статичні властивості й методи

```php
class Counter
{
    private static int $count = 0;

    public static function increment(): int
    {
        return ++self::$count;
    }
}

Counter::increment();   // 1
Counter::increment();   // 2 — стан спільний для ВСІХ викликів, не привʼязаний до екземпляра
```

### 3.2 Константи класу

```php
class HttpStatus
{
    public const int OK = 200;             // типізована константа (PHP 8.3+)
    public const int NOT_FOUND = 404;
}

HttpStatus::OK;   // 200 — звернення через ::, без створення обʼєкта
```

### 3.3 `self` vs `static`: різниця видно лише в успадкуванні

```php
class Model
{
    public const string TABLE = 'models';

    public static function tableViaSelf(): string
    {
        return self::TABLE;      // ЗАВЖДИ Model::TABLE, незалежно від того, хто викликав
    }

    public static function tableViaStatic(): string
    {
        return static::TABLE;     // клас, З ЯКОГО реально викликали
    }
}

class User extends Model
{
    public const string TABLE = 'users';
}

User::tableViaSelf();     // 'models' — self "прив'язаний" до Model, де оголошено метод
User::tableViaStatic();    // 'users'  — static "знає", що виклик прийшов через User
```

Це і є **late static binding**: `self::` вирішується на етапі компіляції
("де написаний код"), `static::` — на етапі виконання ("хто реально викликав").

### 3.4 `new static()`: іменовані конструктори з підтримкою успадкування

```php
class Model
{
    public static function create(): static
    {
        return new static();     // створює екземпляр ФАКТИЧНОГО класу, не завжди Model
    }
}

class User extends Model {}

$user = User::create();   // повертає User, а не Model! Завдяки new static(), не new self()
```

Якби було `new self()`, `User::create()` все одно повернув би `Model` —
типова пастка для фреймворкових базових класів (ActiveRecord-подібні
патерни, тема 43).

### 3.5 `::class` і `final`

```php
User::class;    // 'User' (з неймспейсом, якщо є) — рядок з повною назвою класу,
                  // зручно для типобезпечних посилань замість рядкових літералів

final class Config { /* не можна успадкувати */ }
```

## 4. Підводні камені

- `self::` у методі базового класу **ігнорує** факт виклику через підклас —
  найчастіша причина "чому мій підклас не бачить власну константу/метод"
  у коді, що покладається на статичний поліморфізм.
- Статичний стан (`private static int $count`) — спільний для **всіх**
  місць застосунку, які використовують цей клас, включно з тестами: один
  тест може "забруднити" стан для наступного, якщо лічильник не скидається.
- `new self()` у базовому класі завжди створює екземпляр базового класу,
  навіть якщо викликано через підклас — для фабричних методів, що повинні
  підтримувати успадкування, обов'язково `new static()`.

## 5. Сучасний PHP 8.3+

Типізовані константи класу (`public const int OK = 200;`) — фіча PHP 8.3,
додає перевірку типу до констант так само, як типізовані властивості
додали перевірку до полів у PHP 7.4.

## 6. Контекст екосистеми

Патерн `new static()` — основа "named constructors" (`User::fromRequest(...)`,
`Model::create(...)`) в Eloquent (Laravel ORM) і подібних бібліотеках: базовий
клас пише логіку один раз, а `static::` гарантує, що кожен підклас отримує
екземпляр саме себе, а не базового класу.

## 7. Документація

- https://www.php.net/manual/en/language.oop5.late-static-bindings.php
- https://www.php.net/manual/en/language.oop5.static.php
- https://www.php.net/manual/en/language.oop5.constants.php

## 8. Питання для самоперевірки

1. Що таке late static binding одним реченням?
2. Чому `self::TABLE` у базовому класі поверне значення базового класу,
   навіть якщо викликати через підклас?
3. Чим `new static()` відрізняється від `new self()` у фабричному методі
   базового класу?
4. Чому статичний стан ускладнює тестування?

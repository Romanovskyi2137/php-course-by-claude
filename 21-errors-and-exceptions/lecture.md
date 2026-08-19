# 21. Помилки та винятки

> Що ти вже вмієш: try/catch з іншої мови · Що додає ця тема: PHP-специфічну
> ієрархію `Throwable`, і те, що PHP 8 перетворив колишні "тихі" warnings
> на справжні винятки · ~25 хв

## 1. Якщо ти прийшов з JS / Python / Java

- Ієрархія винятків PHP (`Throwable` → `Error`/`Exception`) схожа на Java
  (`Throwable` → `Error`/`Exception`) майже дослівно — не дивно, це свідоме
  запозичення дизайну. Python має простішу однорівневу ієрархію
  (`BaseException` → `Exception`), JS взагалі не розрізняє "помилки рушія"
  й "винятки застосунку" настільки формально.
- `finally` працює як у Java/JS — виконується завжди, навіть якщо `return`
  був усередині `try` чи `catch`.
- На відміну від Python (де майже все — виняток, включно з `StopIteration`
  для нормального завершення ітерації), у PHP винятки — саме для **помилкових**
  ситуацій, не для звичайного потоку керування.

## 2. Ключові терміни

- **`Throwable`** — базовий інтерфейс і для `Error`, і для `Exception`; `catch (\Throwable $e)`
  ловить буквально все, що можна кинути.
- **`Error`** — помилки самого рушія PHP (типу `TypeError`, `DivisionByZeroError`,
  спроба викликати неіснуючий метод) — типово не для перехоплення в
  бізнес-логіці, радше сигнал бага в коді.
- **`Exception`** — помилки застосунку, які ти кидаєш сам і очікуєш, що
  колись їх спіймають і обробить (`InvalidArgumentException`,
  `RuntimeException`, твої власні класи).
- **Exception chaining (ланцюжок винятків)** — передача оригінального
  винятку як `$previous` у новий, щоб не втратити першопричину.

## 3. Теорія

### 3.1 Ієрархія

```
Throwable (інтерфейс)
├── Error
│   ├── TypeError
│   ├── ValueError
│   ├── DivisionByZeroError
│   └── ArgumentCountError
└── Exception
    ├── InvalidArgumentException  (extends LogicException)
    ├── RuntimeException
    ├── OutOfRangeException
    └── ... (та твої власні класи, extends Exception чи його нащадків)
```

### 3.2 `try`/`catch`/`finally`

```php
try {
    $result = riskyOperation();
} catch (\InvalidArgumentException $e) {
    echo "Некоректні дані: {$e->getMessage()}";
} catch (\RuntimeException $e) {
    echo "Помилка виконання: {$e->getMessage()}";
} finally {
    echo "Виконається завжди — і при винятку, і без нього";
}
```

### 3.3 Множинний catch і non-capturing catch

```php
try {
    process();
} catch (\InvalidArgumentException | \TypeError $e) {   // кілька типів в одному catch
    logError($e);
} catch (\RuntimeException) {                              // PHP 8.0+: без змінної,
    logGenericFailure();                                     // якщо сам обʼєкт не потрібен
}
```

### 3.4 Власні винятки

```php
class InsufficientFundsException extends \RuntimeException
{
}

class Account
{
    public function __construct(private float $balance = 0.0) {}

    public function withdraw(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new InsufficientFundsException(
                "Недостатньо коштів: запит {$amount}, наявно {$this->balance}"
            );
        }
        $this->balance -= $amount;
    }
}
```

Власний клас винятку — це майже завжди просто `extends` вбудованого класу
без додаткового коду: сенс у **типі**, щоб `catch` міг розрізнити конкретну
причину помилки, а не в новій поведінці.

### 3.5 Ланцюжок винятків: `$previous`

```php
try {
    $data = json_decode($raw, flags: JSON_THROW_ON_ERROR);
} catch (\JsonException $e) {
    throw new \RuntimeException('Не вдалось розпарсити конфіг', previous: $e);
    // оригінальний JsonException не втрачається — доступний через $newException->getPrevious()
}
```

### 3.6 PHP 8: warnings стали `Error`

До PHP 8 багато "помилок" видавали лише **warning** у консоль і продовжували
виконання з непередбачуваним результатом (`null`, `false`, порожній рядок).
PHP 8 перетворив найнебезпечніші з них на справжні `Error`:

```php
// PHP 7: Warning: Division by zero, результат — INF (з подальшим сум'яттям)
// PHP 8: DivisionByZeroError — виконання зупиняється, помилку не можна проігнорувати
1 / 0;

// PHP 7: Warning: Undefined variable, $x стає null мовчки
// PHP 8: Warning залишився попередженням для змінних (не Error), але
//         звернення до властивості null (розділ 4) — це вже TypeError
```

## 4. Підводні камені

- `Error` (на відміну від `Exception`) типово **не ловлять** у бізнес-логіці —
  `TypeError`/`ArgumentCountError` майже завжди означають баг у самому коді,
  а не очікувану ситуацію, яку варто "обробити" й продовжити роботу.
- `catch (\Exception $e)` **не ловить** `Error` (і навпаки) — якщо потрібно
  перехопити геть усе (наприклад, на найвищому рівні застосунку для логування
  перед падінням) — `catch (\Throwable $e)`.
- Придушення винятку без дії (`catch (\Exception $e) {}`, порожнє тіло) —
  один з найгірших патернів у PHP-коді: помилка мовчки зникає, а система
  продовжує працювати в непередбачуваному стані. Якщо винятковий стан дійсно
  не критичний — хоча б залоговуй його.

## 5. Сучасний PHP 8.3+

Перетворення критичних warnings на `Error` (PHP 8.0, розділ 3.6) — одна з
найважливіших змін якості PHP: клас помилок, які раніше "тихо ламали" дані
й губились у логах, тепер зупиняють виконання явно й одразу вказують на
проблемну лінію коду.

## 6. Контекст екосистеми

Кожен серйозний PHP-фреймворк має "глобальний exception handler"
(`set_exception_handler`) на межі застосунку: перехоплює будь-який
`Throwable`, що дістався до самого верху, логує його і повертає користувачу
пристойну сторінку помилки замість "сирого" стектрейсу.

## 7. Документація

- https://www.php.net/manual/en/language.exceptions.php
- https://www.php.net/manual/en/class.throwable.php
- https://www.php.net/manual/en/spl.exceptions.php — вбудовані класи винятків SPL

## 8. Питання для самоперевірки

1. Чим `Error` відрізняється від `Exception` за призначенням (не за синтаксисом)?
2. Що ловить `catch (\Throwable $e)`, чого не ловить `catch (\Exception $e)`?
3. Навіщо потрібен параметр `$previous` при кидку нового винятку?
4. Наведи приклад ситуації, яка в PHP 7 була б тихим warning, а в PHP 8 —
   явний `Error`.
